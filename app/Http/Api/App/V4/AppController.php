<?php
namespace App\Http\Api\App\V4;

use DB;
use Log;
use Exception;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Argo\User;
use App\Argo\Project;
use App\Argo\Report;
use App\Argo\Attachment;
use App\Argo\Category;
use App\Argo\Region;
use App\Argo\ReportRaw;
use App\Argo\ProjectStatus;
use App\Argo\ReporterLocation;
use App\Http\Api\App\V4\Model\FormField;
use App\Argo\FieldTemplate;
use App\Http\Api\App\V4\Model\DynamicForm;
use App\Http\Api\App\V4\Model\Container;
use App\Argo\PermissionLevel;
use App\Argo\Tracker;
use App\Services\NotificationService;
use App\Services\ValueService;
use App\Services\TrackerService;
use App\Events\ReportCreated;
use App\Services\ReportArrangeService;
use App\Services\AttachmentService;


class AppController extends ApiController
{
    public function getCurrentVersion()
    {
        try {
            $array_json = array(
                "Version" => config('argodf.app_version')
            );

            $response = response()->json(array('result' => 'success', 'info' => json_encode($array_json)));
            $response->header("Content-Type", "text/html");

            return $response;
        } catch (Exception $e) {
            $response = response()->json(array('result' => 'fail'));
            $response->header("Content-Type", "text/html");

            return $response;
        }
    }

    public function compareVersion(Request $request) {
        info("Input: ".$request->getContent());

        $deviceInfo = json_decode($request->getContent());

        $message = [];
        $avaiable = false;
        $link = '';

        $message['Input'] = (array)$deviceInfo;

        if (config('argodf.app_eliminate_update_notify') == false) {
            if (config('argodf.app_version') != $deviceInfo->version_mark) {
                $message['Comparison'] = "Incoming app version ($deviceInfo->version_mark) is different with server version (".config('argodf.app_version').")";

                $avaiable = true;
            }

            //-- Android device
            if (str_contains(strtolower($deviceInfo->os), 'android')) {
                $link = asset(config('argodf.app_android_download_link'));
            }
        }

        $message['Output'] = [
            'avaiable' => $avaiable,
            'link' => $link,
        ];

        info($message);

        return response()->json([
            'update_available' => $avaiable,
            'link' => $link
        ], 200);
    }


    public function download($version)
    {
        Log::info("Data sync by Argo4 Android App version $version");
        $data = array(
            "User" => $this->getUserList(),
            "Project" => $this->getProjectList(),
            "Form" => $this->getDynamicForm(),
            "FormField" => $this->getFormField(),
            "Field" => $this->getFieldTemplate(),
            "Region" => $this->getRegionlist(),
            "Container" => $this->getContainerList(),
            "PermissionLevel" => $this->getPermissionLevel(),
        );

        return response()->json([
            'result' => 'success',
            'info' => json_encode($data),
        ])
        ->header("Content-Type", "text/html");
    }


    public function submit(Request $request, $version)
    {
        $results = array();
        $attachments = array();
        $metaOnFileName = array();

        $inputs = $request->input();
        $files = $request->file();

        //-- Save raw input to report_raw table
        $reportRawOrm = ReportArrangeService::saveRawReport($inputs, "http");

        $input = json_decode($inputs['info'], true)[0];

        $isTracker = array_get($inputs, 'tracker', false);

        $attachmentsMeta = array_get($input, 'attachment', array());
        $createdAt = array_get($input, 'created_at', new Carbon('now'));
        $formId = array_get($input, 'form_id', null);

        foreach ($attachmentsMeta as $meta) {
            $description = json_decode($meta['description'], true);
            $metaOnFileName[$meta['file_name']] = array(
                'header' => array_get($description, 'header', null),
                'content' => array_get($description, 'content', null),
                'lat' => array_get($description, 'lat', null),
                'lng' => array_get($description, 'lng', null),
                'attached_at' => $createdAt,
                'attached_form_id' => $formId,
            );
        }

        $attachments = $this->uploadFile($files, $metaOnFileName);
        $reportRawOrm->attachments()->sync($attachments);

        try {
            DB::beginTransaction();
            if ($isTracker) {
                $results = $this->submitTracker($input, $attachments);
            } else {
                $results = $this->submitHandler($input, $attachments);
            }
            DB::commit();

            return response()
                    ->json($results, 200)
                    ->header("Content-Type", "text/html");
        } catch (Exception $e) {
            DB::rollBack();

            Log::error("ApiController::submit() : App's version: ($version)");
            Log::error($e);

            return response()
                    ->json($results, 400)
                    ->header("Content-Type", "text/html");
        }
    }


    public function submitHandler($input_data = array(), $attachments = array())
    {
        $notificationService = new NotificationService();

        $results = array();
        $default_project_status_id = ProjectStatus::defaultProjectStatus()->pluck('id');
        $default_container_id = config('argodf.default_container_id');
        try {
            $project_orm = $this->storeProject(array(
                'project_id' => array_get($input_data, 'project_id', 0),
                'project_type' => array_get($input_data, 'project_type', 'unknown'),
                'project_title' => array_get($input_data, 'project_title', null),
                'form_id' => array_get($input_data, 'form_id', null),
                'created_by' => array_get($input_data, 'created_by', 1),
                'created_at' => array_get($input_data, 'created_at', date("Y-m-d H:i:s")),
                'updated_at' => array_get($input_data, 'created_at', date("Y-m-d H:i:s")),
                'group_id' => array_get($input_data, 'group_id', 1),
                'parent_id' => array_get($input_data, 'parent_id', null),
                'container_id' => array_get($input_data, 'container_id', $default_container_id),
                'project_status_id' => array_get($input_data, 'project_status_id', array_get($default_project_status_id, 0, null)),
            ));

            $server_project_id = $project_orm->id;
            if ($input_data['project_type'] == "new") {
                $results = array_merge($results, array(
                    "server_project_id" => $server_project_id
                ));
            }

            $report_orm = $this->storeReport(array(
                'server_project_id' => $server_project_id,
                'description' => array_get($input_data, 'description', null),
                'form_id' => array_get($input_data, 'form_id', null),
                'lat' => array_get($input_data, 'lat', null),
                'lng' => array_get($input_data, 'lng', null),
                'created_by' => array_get($input_data, 'created_by', 1),
                'created_at' => array_get($input_data, 'created_at', date("Y-m-d H:i:s")),
                'updated_at' => array_get($input_data, 'created_at', date("Y-m-d H:i:s")),
                'version' => array_get($input_data, 'version', null),
            ));
            $report_id = $report_orm->id;

            if (!empty($attachments)) {
                $report_orm->attachments()->sync($attachments);
            }

            //-- Create report_values
            if (array_key_exists('fields', $input_data)) {
                $this->storeReportFormValue($report_orm, array_get($input_data, 'fields', array()));
            }

            if (array_key_exists('region', $input_data)) {
                $this->storeProjectRegion($project_orm, array_get($input_data, 'region', array()));
            }

            event(new ReportCreated($report_id));

            //-- Setup by_update notification (if necessary)
            $reporterEmail = array_get($input_data, 'reporter_email', false);
            if ($reporterEmail) {
                $notificationService->setByUpdateMailNotification(
                    'App\Argo\Project',
                    $server_project_id,
                    array_get($input_data, 'reporter_name', ''),
                    $reporterEmail
                );
            }

            //-- Send by_update notification
            $notificationService->sendEntityUpdatedNotification(
                'App\Argo\Project',
                $server_project_id
            );

            $results = array_merge($results, array(
                'result' => 'success',
                'report_id' => $report_id,
            ));

            return $results;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function submitReporterLocation(Request $request)
    {
        try {
            $data = json_decode($request->get('info'));

            $reporter_location = new ReporterLocation();
            $reporter_location->device_id = $data->device_id;
            $reporter_location->lat = $data->lat;
            $reporter_location->lng = $data->lng;
            $reporter_location->created_by = $data->created_by;
            $reporter_location->save();
        } catch (Exception $e) {
            Log::error($e);
            return response()->make(array(
                'status'=> 'Update reporter location failed'
            ), 400);
        }

        return response()->make(array(
            'status' => 'success',
            'created_at' => $reporter_location->created_at->toDateTimeString()
        ), 200);
    }


    private function uploadFile($files, $metaOnFileName = array())
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(900);

        $pivotOnAttachId = array();

        $attachService = new AttachmentService();
        foreach ($files as $file) {
            $attachment = $attachService->storeFile($file);
            $attachName = $attachment['name'];
            $attachId = $attachment['id'];
            $metaOnAttachName = array_get($metaOnFileName, $attachName, array());
            $pivotOnAttachId[$attachId] = array(
                'attached_at' => array_get($metaOnAttachName, "attached_at", new Carbon('now')),
                'attached_form_id' => array_get($metaOnAttachName, "attached_form_id", null),
                'description' => json_encode(array(
                    'header' => array_get($metaOnAttachName, "header", ""),
                    'content' => array_get($metaOnAttachName, "content", ""),
                    'lat' => array_get($metaOnAttachName, "lat", ""),
                    'lng' => array_get($metaOnAttachName, "lng", ""),
                )),
            );
        }

        return $pivotOnAttachId;
    }

    // ---------------------- review line -------------------------

    public function submitBySms($input_data = array())
    {
        return $this->submit($input_data);
    }

    public function submitTracker($input_data = array(), $attachments = array())
    {
        $results = array();
        try {
            $tracker = array(
                "title" => array_get($input_data, "title", null),
                "path" => array_get($input_data, "path", null),
                "created_by" => array_get($input_data, "created_by", null),
                "created_at" => array_get($input_data, "created_at", new Carbon('now')),
                "updated_at" => array_get($input_data, "updated_at", new Carbon('now')),
            );

            $tracker_orm = $this->storeTracker($tracker);

            $inputPath = $tracker_orm->path[0];
            $trackerService = new TrackerService($tracker_orm->id, 'tracker');
            $trackerMeta = $trackerService->calcTrackerMeta($inputPath)->getResult();
            $tracker_orm->meta = $trackerMeta;
            $tracker_orm->save();

            if (!empty($attachments)) {
                $tracker_orm->attaches()->sync($attachments);
            }

            $results = array_merge($results, array(
                'result' => 'success',
            ));

            return $results;
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function getUserList()
    {
        return User::select(
            array(
                'id',
                'name',
                'permission_level_id',
                'email',
                DB::raw('password AS pwd')
            )
        )
        ->whereNotIn('permission_level_id', [1, 5])
        ->get();
    }

    private function getProjectList()
    {
        return Project::withTrashed()
        ->select(
            array(
                'id',
                'title',
                'description',
                'default_form_id',
                'project_status_id',
                'created_by',
                'created_at',
                'updated_at',
                'deleted_at',
                'parent_id',
                'container_id',
                'edit_level_id'
            )
        )
        ->with(array(
            'owners' => function ($query) {
                $query->select('id');
            },
            'edit_level' => function ($query) {
                $query->select(array('id', 'priority'));
            }
        ))
        ->get();
    }

    private function getDynamicForm()
    {
        return DynamicForm::get(
            array(
                'id',
                'name',
                'is_photo_required'
            )
        );
    }

    private function getFormField()
    {
        return FormField::leftJoin('permission_level', 'permission_level.id', '=', 'form_field.edit_level_id')
        ->get(
            array(
                'form_field.id',
                'form_field.name',
                'form_field.form_id',
                'form_field.field_template_id',
                'form_field.default_value',
                'form_field.options',
                'form_field.order',
                'permission_level.priority AS edit_level_priority',
                'form_field.show_if',
                'form_field.is_required',
                'form_field.formula'
            )
        );
    }

    private function getFieldTemplate()
    {
        return FieldTemplate::get(
            array(
                'id',
                'name',
                'key as html'
            )
        );
    }

    private function getRegionlist()
    {
        return Region::select(
            array(
                'id',
                'parent_id',
                'order',
                'name',
                'label_name'
            )
        )
        ->with(array('projects' => function ($query) {
            $query->select(array(
                'id'
            ));
        }))->get();
    }

    private function getContainerList()
    {
        return Container::select(
            array(
                'id',
                'name',
                'parent_id',
                'form_id',
                'reportable'
            )
        )
        ->get();
    }

    private function getPermissionLevel()
    {
        return PermissionLevel::all();
    }

    private function storeProject($project_data)
    {
        if ($project_data['project_type'] == "new") {
            $project_orm = new Project;
            $project_orm->title = $project_data['project_title'];
            $project_orm->default_form_id = $project_data['form_id'];

            $project_orm->created_by = $project_data['created_by'];
            $project_orm->created_at = $project_data['created_at'];
            $project_orm->updated_at = $project_data['updated_at'];

            $project_orm->view_level_id = config('argodf.default_perm.project_for_app.view');
            $project_orm->edit_level_id = config('argodf.default_perm.project_for_app.edit');

            //-- The master/slave site structure, set it to 1 as the only master
            $project_orm->group_id = $project_data['group_id'];
            $project_orm->parent_id = $project_data['parent_id'];
            $project_orm->container_id = $project_data['container_id'];
            $project_orm->project_status_id = $project_data['project_status_id'];

            $project_orm->save();
        } elseif ($project_data['project_type'] == "server") {
            $project_orm = Project::findOrFail($project_data['project_id']);
        } else {
            $project_orm = Project::findOrFail(0);
        }
        return $project_orm;
    }

    private function storeReport($report_data)
    {
        $report_orm = new Report;
        $report_orm->project_id = $report_data['server_project_id'];
        $report_orm->description = $report_data['description'];
        $report_orm->form_id = $report_data['form_id'];
        $report_orm->lat = $report_data['lat'];
        $report_orm->lng = $report_data['lng'];

        $report_orm->view_level_id = config('argodf.default_perm.report.view');
        $report_orm->edit_level_id = config('argodf.default_perm.report.edit');

        $report_orm->created_by = $report_data['created_by'];
        $report_orm->created_at = $report_data['created_at'];
        $report_orm->updated_at = $report_data['updated_at'];
        $report_orm->version = $report_data['version'];

        $report_orm->save();
        return $report_orm;
    }

    private function storeReportFormValue($report_orm, $fields)
    {
        $report_fields = array();
        $tracker_fields = FormField::withTrashed()
            ->select('id')
            ->where('field_template_id', '=', 8)
            ->get()
            ->toArray();

        $tracker_field_ids = array_map(function ($field) {
            return $field['id'];
        }, $tracker_fields);

        foreach ($fields as $field) {
            $form_field_id = $field['form_field_id'];

            if (in_array($form_field_id, $tracker_field_ids)) {
                $valueService = new ValueService($report_orm->id, 'report');
                $generateResult = $valueService->calcTrackerMeta([
                        $form_field_id => $field['value']
                    ])
                    ->getResult();
                $field_value = $generateResult[$form_field_id];
            } else {
                $field_value = $field['value'];
            }

            $report_fields[$form_field_id] = array('value' => $field_value);
        }

        $report_orm->form_fields()->sync($report_fields);
    }

    private function storeProjectRegion($project_orm, $regions)
    {
        $region_ids = array();

        if (!empty($regions)) {
            $region_ids = array_map(function ($region) {
                return $region['id'];
            }, $regions);
        } else {
            $region_ids = config('argodf.default_region_ids');
        }

        $project_orm->regions()->sync($region_ids);
    }

    private function storeTracker($tracker_data)
    {
        $tracker_orm = new Tracker();
        $tracker_orm->title = $tracker_data['title'];
        $tracker_orm->path = [json_decode($tracker_data['path'], true)];
        $tracker_orm->created_by = $tracker_data['created_by'];
        $tracker_orm->created_at = $tracker_data['created_at'];
        $tracker_orm->updated_at = $tracker_data['updated_at'];

        $tracker_orm->save();
        return $tracker_orm;
    }
}
