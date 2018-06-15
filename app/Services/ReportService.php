<?php

namespace App\Services;

use App\Argo\PermissionLevel;
use App\Argo\Project;
use App\Argo\ProjectValue;
use App\Argo\Report;
use App\Events\ProjectValueUpdated;

class ReportService
{
    public $reportId;
    private $report;
    private $createdByPermissionLevelId;

    public function __construct($reportId)
    {
        $this->reportId = $reportId;
    }

    public function autoMerge()
    {
        $this->report = Report::with([
            'dynamic_form' => function ($query) {
                $query->with([
                    'fields' => function ($query) {
                        $query->with([
                            'report_value' => function ($query) {
                                $query->where('report_id', '=', $this->reportId);
                            }
                        ])
                            ->select(['id', 'form_id', 'edit_level_id']);
                    }
                ])
                    ->select(['id', 'deleted_at']);
            },
            'files' => function ($query) {
                $query->select(['attachment.id'])
                    ->withPivot('description');
            },
            'project' => function ($query) {
                $query->select(['id', 'lat', 'lng', 'description', 'edit_level_id'])
                    ->withTrashed();
            },
            'creator' => function ($query) {
                $query->select(['id', 'permission_level_id']);
            }
        ])
            ->select([
                'id',
                'form_id',
                'project_id',
                'lat',
                'lng',
                'description',
                'region_ids',
                'view_level_id',
                'created_by',
            ])
            ->findOrFail($this->reportId);

        try {
            \DB::beginTransaction();

            $this->createdByPermissionLevelId = is_null($this->report->creater) ? config('argo.default_perm.project.edit') : $this->report->creater->permission_level_id;
            $projectEditLevelId = !is_null($this->report->project) && !is_null($this->report->project->edit_level_id) ? $this->report->project->edit_level_id : config('argo.default_perm.project.edit');

            $this->mergeValuesToProject();

            if ($this->createdByPermissionLevelId <= $projectEditLevelId) {
                $this->mergeBasicInfoToProject();
            }

            $this->updateProjectCoverImageByReport();

            if ($this->report->region_ids) {
                $this->updateProjectRegionsByReport();
            }

            $this->makeProjectAndReportPublic();

            if (count($this->report->files) > 0) {
                $this->mergeAttachmentsToProject();
            }

            if ($this->report->project) {
                $this->report->project->touch();
            }

            \DB::commit();

            return $this;
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error($e);
            throw $e;
        }
    }

    public function mergeValuesToProject()
    {
        $insertProjectValues = [];
        $formFieldIds = [];
        foreach (array_get($this->report->dynamic_form, 'fields', array()) as $field) {
            if ($field->report_value && $field->edit_level_id >= $this->createdByPermissionLevelId) {
                array_push($insertProjectValues, [
                    'project_id' => $this->report->project_id,
                    'form_field_id' => $field->report_value->form_field_id,
                    'value' => $field->report_value->value
                ]);
                array_push($formFieldIds, $field->report_value->form_field_id);
            }
        }

        if (count($insertProjectValues) > 0) {
            ProjectValue::whereIn('form_field_id', $formFieldIds)
                ->where('project_id', '=', $this->report->project_id)
                ->delete();

            \DB::table('project_value')->insert($insertProjectValues);

            if (Project::find($this->report->project_id)) {
                event(new ProjectValueUpdated($this->report->project_id));
            }
        }
    }

    public function mergeBasicInfoToProject()
    {
        if (($this->report->lat and $this->report->lng) || $this->report->description) {
            $this->report->project->lat = $this->report->lat != NULL ? $this->report->lat : $this->report->project->lat;
            $this->report->project->lng = $this->report->lng != NULL ? $this->report->lng : $this->report->project->lng;
            $this->report->project->description = $this->report->description != NULL ? $this->report->description : $this->report->project->description;
            $this->report->project->save();
        }
    }

    public function updateProjectCoverImageByReport()
    {
        $reportId = $this->report->id;

        $lastReportImage = \DB::table('attachables')
            ->select('attachment_id')
            ->leftJoin('attachment', 'attachment.id', '=', 'attachables.attachment_id')
            ->where('attachables.attachable_id', '=', $reportId)
            ->where('attachables.attachable_type', '=', 'App\Argo\Report')
            ->where('attachment.type', 'LIKE', 'image%')
            ->orderBy('attachables.attached_at', 'DESC')
            ->first();

        if ($this->report->project && $lastReportImage) {
            $this->report->project->cover_image_id = $lastReportImage->attachment_id;
            $this->report->project->save();

            \Log::info("ProjectValueService@auto_merge project $this->report->project_id cover_image been updated to " . $lastReportImage->attachment_id);
        }
        return;
    }

    public function updateProjectRegionsByReport()
    {
        $project = Project::withTrashed()
            ->select('id')
            ->find($this->report->project_id);

        $regionIds = json_decode($this->report->region_ids);
        $project->regions()
            ->sync($regionIds);
    }

    public function makeProjectAndReportPublic()
    {
        $maxViewLevelId = PermissionLevel::max('priority');

        if ($this->report->view_level_id != $maxViewLevelId) {
            $this->report->view_level_id = $maxViewLevelId;
            $this->report->save();
        }

        if ($this->report->project->view_level_id != $maxViewLevelId) {
            $this->report->project->view_level_id = $maxViewLevelId;
            $this->report->project->save();
        }
    }

    public function mergeAttachmentsToProject()
    {
        $attachIds = array();
        foreach ($this->report->files as $attachment) {
            $attachIds[$attachment->id] = array(
                'attached_form_id' => $this->report->form_id,
                'attached_at' => $attachment->pivot->attached_at,
                'description' => $attachment->pivot->description
            );
        }

        $this->report->project->files()
            ->detach(array_pluck($this->report->files, 'id'));

        $this->report->project->files()
            ->attach($attachIds);
    }
}
