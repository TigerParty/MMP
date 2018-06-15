<?php

namespace App\Http\Api\App\V5;

use App\Events\ReportCreated;
use Illuminate\Support\Facades\DB;
use Log;
use Carbon\Carbon;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Argo\Project;
use App\Argo\User;
use App\Argo\DynamicForm;
use App\Argo\Region;
use App\Argo\Container;
use App\Argo\ProjectStatus as Status;
use App\Argo\ReportRaw;
use App\Argo\Report;
use App\Argo\RegionLabel;

use App\Http\Controllers\AttachmentController;


class AppController extends BaseController
{
    use DispatchesJobs;

    function download(Request $request)
    {
        $projects = Project::selectRaw('
            id,
            parent_id,
            container_id,
            title,
            description,
            project_status_id AS status_id,
            edit_level_id
        ')->with([
            'owners' => function($query){
                $query->select('id');
            },
            'regions' => function($query){
                $query->select('id');
            },
            'edit_level' => function($query){
                $query->select(['id', 'priority']);
            }
        ])
        ->get();

        foreach($projects as $project)
        {
            $owner_ids = array_pluck($project->owners, 'id');
            unset($project->owners);
            $project->owners = $owner_ids;

            $region_ids = array_pluck($project->regions, 'id');
            unset($project->regions);
            $project->regions = $region_ids;
        }

        $users = User::selectRaw('
            user.id,
            user.name,
            user.password,
            permission_level.name AS perm,
            permission_level.priority AS perm_level
        ')
        ->leftJoin('permission_level', 'user.permission_level_id', '=', 'permission_level.id')
        ->where('permission_level.priority', '>', 1)
        ->get()
        ->makeVisible([
            'password'
        ]);

        $forms = DynamicForm::select('id', 'name', 'is_photo_required')
        ->with([
            'fields' => function($query){
                $query->select([
                    'form_field.id',
                    'form_field.name',
                    'form_field.form_id',
                    'form_field.options',
                    'view_perm.priority AS view_level',
                    'edit_perm.priority AS edit_level',
                    'form_field.field_template_id AS template_id',
                    'field_template.key AS field_template AS template_key',
                    'form_field.order',
                    'form_field.show_if',
                    'form_field.is_required',
                ])
                ->leftJoin('permission_level AS view_perm', 'view_perm.id', '=', 'form_field.view_level_id')
                ->leftJoin('permission_level AS edit_perm', 'edit_perm.id', '=', 'form_field.edit_level_id')
                ->leftJoin('field_template', 'field_template.id', '=', 'form_field.field_template_id')
                ->whereNull('form_field.formula')
                ->whereNotIn('field_template.key', ['gps_tracker', 'check_box_group', 'iri_tracker']);
            }
        ])
        ->get();

        $regions = Region::selectRaw('
            id,
            `name`,
            parent_id,
            label_name,
            `order`
        ')
        ->get();

        $regionLabels = $regionLabels = RegionLabel::orderBy('order')->get();

        $containers = Container::selectRaw('
            id,
            parent_id,
            form_id,
            name,
            title_duplicatable,
            uid_rule
        ')
        ->where('reportable', '=', 1)
        ->get();

        $status = Status::select('id', 'name', 'default')
        ->get();

        return response()->make([
            'project' => $projects,
            'user' => $users,
            'form' => $forms,
            'region' => $regions,
            'region_label' => $regionLabels,
            'container' => $containers,
            'status' => $status,
        ]);
    }

    function submit(Request $request)
    {
        $contentRaw = $request->getContent();

        $content = json_decode($contentRaw);

        if($content == null) {
            abort(400, "Format not correct.");
        }

        $reportRaw = new ReportRaw();
        $reportRaw->payload = $contentRaw;
        $reportRaw->source = 'http';
        $reportRaw->save();

        try {
            DB::beginTransaction();

            info((array)$content);

            $projectId = object_get($content, 'project_id', null);
            //-- create new project
            if ($projectId == null) {
                $project = Project::create([
                    'group_id' => config('argodf.group_id')[0],
                    'title' => object_get($content, 'title', null),
                    'description' => object_get($content, 'description', null),
                    'view_level_id' => config('argodf.default_perm.project_for_app.view'),
                    'edit_level_id' => config('argodf.default_perm.project_for_app.edit'),
                    'project_status_id' => object_get($content, 'status_id', null),
                    'default_form_id' => object_get($content, 'form_id', null),
                    'lat' => object_get($content, 'lat', null),
                    'lng' => object_get($content, 'lng', null),
                    'container_id' => config('argodf.default_container_id'),
                    'created_by' => object_get($content, 'created_by', null),
                ]);
            }
            //-- update old project
            else if($projectId != null) {
                $project = Project::find($projectId);
                $project->title = object_get($content, 'title', null);
                $project->description = object_get($content, 'description', null);
                $project->project_status_id = object_get($content, 'status_id', null);
                $project->lat = object_get($content, 'lat', null);
                $project->lng = object_get($content, 'lng', null);
                $project->save();
            }

            //-- region
            $regions = array_filter(object_get($content, 'regions', []));
            if (count($regions)){
                $project->regions()->sync($regions);
            }

            //-- report
            $report = Report::create([
                'form_id' => object_get($content, 'form_id', null),
                'project_id' => $project->id,
                'description' => $project->description,
                'lat' => $project->lat,
                'lng' => $project->lng,
                'view_level_id' => config('argodf.default_perm.report.view'),
                'edit_level_id' => config('argodf.default_perm.report.edit'),
                'created_by' => object_get($content, 'created_by', null),
            ]);

            //-- fields
            $fields = object_get($content, 'fields', []);
            $fieldData = [];
            if (count($fields)) {
                foreach ($fields as $field) {
                    $fieldData[$field->id] = [
                        'value' => object_get($field, 'value', null)
                    ];
                }
                $report->form_fields()->sync($fieldData);
            }

            //-- attachments
            $attachs = object_get($content, 'attachs', []);
            $attachData = [];
            if (count($attachs)) {
                foreach ($attachs as $attach) {
                    $attachData[$attach->id] = [
                        'attached_form_id' => $attach->form_id,
                        'attached_at' => Carbon::now(),
                        'description' => $attach->desc ? json_encode($attach->desc) : null
                    ];
                }
                $report->attachments()->sync($attachData);
            }

            event(new ReportCreated($report->id));

            DB::commit();
        } catch (\Exception $e) {
            Log::error($e);

            DB::rollback();

            return response()->json(['status' => $e->getMessage()], 400);
        }

        return response()->json(["status" => "ok"], 200);
    }

    function uploadFile(Request $request)
    {
        $controller = new AttachmentController();

        if ($request->hasFile('file'))
        {
            $file = $request->file('file');
            return $controller->doUpload($file);
        }

        return response()->json([
            "status" => "file not attached"
        ], 400);
    }
}
