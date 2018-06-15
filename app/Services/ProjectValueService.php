<?php
namespace App\Services;

use App\Argo\User;
use App\Argo\Project;
use App\Argo\Report;
use App\Argo\DynamicForm;
use App\Argo\ProjectValue;
use App\Argo\PermissionLevel;

class ProjectValueService
{
    public function autoMerge($report_id)
    {
        $report = Report::with([
                'dynamic_form' => function($query) use ($report_id) {
                    $query->with([
                        'fields' => function($query) use ($report_id) {
                            $query->with([
                                'report_value' => function($query) use ($report_id) {
                                    $query->where('report_id', '=', $report_id);
                                }
                            ])
                            ->select(['id','form_id', 'edit_level_id']);
                        }
                    ])
                    ->select(['id', 'deleted_at']);
                },
                'files' => function($query) {
                    $query->select(['attachment.id'])->withPivot('description');
                },
                'project' => function($query) {
                    $query->select(['id', 'edit_level_id']);
                },
                'creater' => function($query) {
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
                'category_ids',
                'view_level_id',
                'created_by',
            ])
            ->findOrFail($report_id);
        try
        {
            $created_by_permission_level_id = is_null($report->creater) ? config('argodf.default_perm.project.edit') : $report->creater->permission_level_id;
            $project_edit_level_id = !is_null($report->project) && !is_null($report->project->edit_level_id) ? $report->project->edit_level_id : config('argodf.default_perm.project.edit');

            $report_values = [];
            foreach (array_get($report->dynamic_form, 'fields', array()) as $field)
            {
                if($field->report_value && $field->edit_level_id >= $created_by_permission_level_id)
                {
                    array_push($report_values, $field->report_value);
                }
            }

            \DB::beginTransaction();

            $this->updateProjectValues($report->project_id, $report_values);

            if($created_by_permission_level_id <= $project_edit_level_id) {
                $this->updateProjectColumns($report->project_id, $report->lat, $report->lng, $report->description);
            }

            $this->updateProjectCoverImage($report);

            if($report->region_ids)
            {
                $this->updateProjectRegions($report->project_id, json_decode($report->region_ids));
            }

            $this->publicProjectAndReport($report);

            if(count($report->files) > 0)
            {
                $this->updateProjectAttachments($report->project_id, $report->files, $report->form_id);
            }
            \DB::commit();

            return $report_id;
        }
        catch(\Exception $e)
        {
            \DB::rollback();
            \Log::error($e);
            throw $e;
        }
    }

    public function updateProjectValues($project_id, $report_values)
    {
        $form_field_ids = [];
        $insert_project_values = [];
        foreach ($report_values as $report_value)
        {
            array_push($form_field_ids, $report_value->form_field_id);

            $insert_project_value = array(
                    'project_id' => $project_id,
                    'form_field_id' => $report_value->form_field_id,
                    'value' => $report_value->value
                );

            array_push($insert_project_values, $insert_project_value);
        }

        $project_values = ProjectValue::whereIn('form_field_id', $form_field_ids)
            ->where('project_id', '=', $project_id)
            ->delete();

        \DB::table('project_value')->insert($insert_project_values);
    }

    public function updateProjectColumns($project_id, $lat, $lng, $description)
    {
        if($lat and $lng)
        {
            $project = Project::withTrashed()->find($project_id);
            $project->lat = $lat;
            $project->lng = $lng;
            $project->description = $description;
            $project->save();
        }
    }

    public function updateProjectRegions($project_id, $region_ids)
    {
        $project = Project::withTrashed()->select('id')->find($project_id);
        $project->regions()->sync($region_ids);
    }

    /**
     * @deprecated
     */
    public function update_project_categories($project_id, $category_ids)
    {
        $project = Project::withTrashed()->select('id')->find($project_id);

        $project_category_ids = $project->categories()->pluck('id')->toArray();

        foreach ($category_ids as $category_id)
        {
            if(!in_array($category_id, $project_category_ids))
            {
                array_push($project_category_ids, $category_id);
            }
        }

        $project->categories()->sync($project_category_ids);
    }

    public function updateProjectCoverImage($report)
    {
        $reportId = $report->id;

        $last_report_image = \DB::table('attachables')
            ->leftJoin('attachment', 'attachment.id', '=', 'attachables.attachment_id')
            ->where('attachables.attachable_id', '=', $reportId)
            ->where('attachables.attachable_type', '=', 'App\Argo\Report')
            ->where('attachment.type', 'LIKE', 'image%')
            ->orderBy('attachables.attached_at', 'DESC')
            ->take(1)
            ->get(['attachment_id']);

        if($report->project && $last_report_image)
        {
            $report->project->cover_image_id = $last_report_image[0]->attachment_id;
            $report->project->save();
            \Log::info("ProjectValueService@auto_merge project $report->project_id cover_image been updated to ".$last_report_image[0]->attachment_id);
        }
        return;
    }

    public function publicProjectAndReport($report)
    {
        $max_view_level_id = PermissionLevel::max('priority');

        if($report->view_level_id != $max_view_level_id)
        {
            $report->view_level_id = $max_view_level_id;
            $report->save();
        }

        $project = Project::withTrashed()->select(['id', 'view_level_id'])->find($report->project_id);
        if($project->view_level_id != $max_view_level_id)
        {
            $project->view_level_id = $max_view_level_id;
            $project->save();
        }
    }

    public function updateProjectAttachments($project_id, $attachments, $form_id = null)
    {
        $attach_ids = array();
        foreach ($attachments as $key => $attachment) {
            $attach_ids[$attachment->id] = array(
                'attached_form_id' => $form_id,
                'attached_at' => $attachment->pivot->attached_at,
                'description' => $attachment->pivot->description
            );
        }

        $project = Project::withTrashed()->select('id')->find($project_id);
        $project->attachments()->detach(array_pluck($attachments, 'id'));

        $project->attachments()->attach($attach_ids);
    }

    public function arrangeValueKeyByFieldId($fieldValues, $requireDecodeFieldIds = false) {
        if ($requireDecodeFieldIds === false) {
            $requireDecodeFieldIds = FormField::select('id')
                ->whereIn('field_template_id', [9])
                ->pluck('id')
                ->all();
        }

        $arrangedValues = [];
        foreach($fieldValues as $fieldValue) {
            if(in_array($fieldValue->form_field_id, $requireDecodeFieldIds))
            {
                $multiValues = json_decode($fieldValue->value);
                $decodedValues = [];
                if ($multiValues) {
                    foreach ($multiValues as $value) {
                        $decodedValues[$value] = true;
                    }
                }
                $fieldValue->value = $decodedValues;
            }

            $arrangedValues[$fieldValue->form_field_id] = $fieldValue;
        }
        return $arrangedValues;
    }

    public function parseGpsTracker($gpsTrackerField)
    {
        $gpsTrackerField = json_decode($gpsTrackerField);

        return $gpsTrackerField;
    }
}
