<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Exception;
use Log;
use Auth;
use Validator;

use App\Repositories\ReportRepository;
use App\Repositories\ReportValueRepository;
use App\Repositories\AttachmentRepository;
use App\Services\NotificationService;
use App\Argo\Project;
use App\Argo\Report;
use App\Argo\Region;
use App\Argo\DynamicForm;
use App\Argo\PermissionLevel;
use App\Events\ReportCreated;

class ReportController extends Controller {
    public function create($p_id)
    {
        $access_priority = argo_current_permission();

        $regions = Region::whereNull('parent_id')->orderBy('name', 'ASC')->get();

        $dynamic_forms = DynamicForm::GetDynamicForms($access_priority)->get();

        foreach ($dynamic_forms as $dynamic_form) {
            $dynamic_form->use_default = true;
        }

        $project = Project::with(array(
            'regions',
            'container' => function($query) {
                $query->select(['id', 'form_id']);
            }
        ))
        ->where('id', '=', $p_id)
        ->first(['id', 'title', 'edit_level_id', 'container_id']);

        $pms_levels = PermissionLevel::GetAvailableLevels($access_priority);

        if($access_priority <= $project->edit_level_id)
        {
            $array_data = array(
                'project' => $project,
                'regions' => $regions,
                'dynamic_forms' => $dynamic_forms,
                'pms_levels'  => $pms_levels,
            );

            return view('report.create', $array_data);
        }
        else
        {
            abort(400);
        }
    }

    public function store(Request $request, $p_id)
    {
        $project = $request->input('project');
        $report = $request->all();

        try
        {
            DB::beginTransaction();

            $new_report = new Report;
            $new_report->version = "web";
            $new_report->project_id = $p_id;
            $new_report->lat = $report['report']['lat'];
            $new_report->lng = $report['report']['lng'];
            $new_report->created_by = Auth::user()->id;
            $new_report->description = $report['report']['description'];

            $region_ids = array_where(array_pluck($project['regions'], 'id'), function($value, $key) {
                return is_integer((int)$value) and $value > 0;
            });

            if(array_key_exists('regions', $project)) {
                $new_report->region_ids = json_encode($region_ids);
            }

            if($report['report']['dynamic_form']['id'] != "")
            {
                $new_report->form_id = $report['report']['dynamic_form']['id'];
            }

            if (argo_is_accessible(config('argodf.admin_function_priority')))
            {
                $new_report->view_level_id = $report['report']['view_level'] != "" ? $report['report']['view_level'] : config('argodf.default_perm.report.view');
                $new_report->edit_level_id = $report['report']['edit_level'] != "" ? $report['report']['edit_level'] : config('argodf.default_perm.report.edit');
            }
            else
            {
                $new_report->view_level_id = config('argodf.default_perm.report.view');
                $new_report->edit_level_id = config('argodf.default_perm.report.edit');
            }

            $new_report->save();
            $report_id = $new_report->id;

            if(array_key_exists('fields', $report['report']['dynamic_form']))
            {
                foreach ($report['report']['dynamic_form']['fields'] as $field) {
                    $value = null;
                    if (array_key_exists('value', $field))
                    {
                        $value = $field['value'];
                        $new_report->form_fields()->attach($field['id'], array('value' => $value));
                    }

                    // store multiple checkbox value
                    if (array_key_exists('group', $field))
                    {
                        $group = json_decode($field['group']['value'], true);

                        foreach ($group as $option => $check) {
                            if($check){
                                $value[] = $option;
                            }
                        }

                        if (!empty($value)) {
                            $value = json_encode($value);
                        }

                        $new_report->form_fields()->attach($field['group']['id'], array('value' => $value));
                    }
                }
            }

            $new_attachment_ids = array();
            foreach ($request->input('attachIDs', array()) as $key => $value) {
                $new_attachment_ids[$value] = array('attached_at' => $new_report->created_at);
            }

            //-- Attach report attachments
            if (count($new_attachment_ids) > 0) {
                $new_report->attachments()->sync($new_attachment_ids);
            }

            DB::commit();

            //-- Send updated notification for the report's project
            $notificationService = new NotificationService();
            $notificationService->sendEntityUpdatedNotification(
                'App\Argo\Project',
                $p_id
            );

            event(new ReportCreated($new_report->id));

            return redirect('/report/'. $new_report->id);
        }
        catch(Exception $e)
        {
            DB::rollback();
            Log::error($e);
            abort(400);
        }
    }

    public function show($rp_id)
    {
        $checkId = Validator::make(
            array('rp_id' => $rp_id),
            array('rp_id' => 'exists:report,id')
        );
        if($checkId->fails())
        {
            abort(404);
        }

        $report = Report::find($rp_id);

        $project = Project::with('edit_level')
            ->where('id', '=', $report->project_id)
            ->first(['id', 'title', 'edit_level_id']);

        if (argo_is_accessible($report->view_level->priority))
        {
            if (!argo_is_accessible(config('argodf.admin_function_priority')))
            {
                unset($report->view_level);
                unset($report->edit_level);
            }

            $array_data = array(
                'project' => $project,
                'report' => $report
            );

            return view('report.show', $array_data);
        }
        else
        {
            return redirect(asset('/login'));
        }
    }

    public function showApi(Request $request, $report_id)
    {
        $report_repo = new ReportRepository();
        $report_value_repo = new ReportValueRepository();
        $attach_repo = new AttachmentRepository();

        //-- basic
        $report = $report_repo->basicInfo()->findOrFail($report_id);

        //-- regions
        $regions = array();
        $region_ids = array_get($report, 'region_ids', '[]');
        if($region_ids)
        {
            $region_ids = json_decode($region_ids);
            $regions = Region::whereIn('id', $region_ids)->orderBy('id')->get(['name', 'label_name']);
        }

        foreach ($regions as $region)
        {
            $region->label_name = ucfirst($region->label_name);
        }

        //-- notifications
        $notifications = $report->notifications;

        //-- report values
        $report_values = $report_value_repo->getValuesOnSaved($report_id);

        //-- attachments
        $attachments = $attach_repo->getPageAttachments($report_id, "App\Argo\Report");

        return response()->make(array(
            'basic_info' => array(
                'id' => $report_id,
                'view_level' => $report->view_level,
                'edit_level' => $report->edit_level,
                'project_id' => $report->project_id,
                'project_title' => $report->project_title,
                'regions' => $regions,
                'description' => $report->description,
                'lat' => $report->lat,
                'lng' => $report->lng,
                'created_by' => $report->created_by,
                'updated_by' => $report->updated_by,
                'updated_at' => $report->updated_at->format(config('argodf.php_datetime_format')),
                'version' => $report->version,
                'form_name' => $report->dynamic_form_name
            ),
            'notifications' => $notifications,
            'fields' => $report_values,
            'attachments' => $attachments
        ));
    }

    public function destroy(Request $request, $p_id, $rp_id)
    {
        if(argo_is_accessible(config('argodf.delete_priority')))
        {
            try
            {
                DB::beginTransaction();

                $report = Report::findOrFail($rp_id);
                $report->attachments()->detach();
                $report->images()->detach();
                $report->form_fields()->detach();
                $report->delete();

                DB::commit();

                if($request->ajax())
                {
                    return response('success', 200);
                }
                else
                {
                    return redirect('project/'. $p_id);
                }
            }
            catch(Exception $e)
            {
                DB::rollback();
                abort(400);
            }
        }
        else
        {
            abort(400);
        }
    }
}
