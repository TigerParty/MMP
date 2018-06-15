<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Log;
use Auth;

use App\Services\AggregationService;
use App\Repositories\ProjectRepository;
use App\Repositories\ProjectValueRepository;
use App\Repositories\AttachmentRepository;
use App\Repositories\ContainerRepository;
use App\Repositories\FormFieldRepository;
use App\Argo\DynamicForm;
use App\Argo\Project;
use App\Argo\Report;
use App\Argo\Container;
use App\Argo\Aggregation;

class ProjectController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Citizen project detail page functions
    |--------------------------------------------------------------------------
    |
    | Functions for Form sub-routes under project
    |
    */
    public function show($id)
    {
        $projectRepo = new ProjectRepository();

        $project = $projectRepo->basicInfo()
            ->with(array(
                'container' => function($query) {
                    $query->with('form');
                },
                'view_level'
            ))
            ->findOrFail($id);
        $view_level = $project->view_level->priority;

        if (!argo_is_accessible($view_level)) {
            abort(403);
        }

        $breadcrumbs = $projectRepo->getBreadcrumb($project->parent_id, [
            ['id' => $project->container_id, 'type' => 'container', 'title' => $project->container->name, 'project_id' => $project->parent_id],
            ['id' => $project->id, 'type' => 'project', 'title' => $project->title]
        ]);

        $extraTitle = array();
        foreach ($breadcrumbs as $index => $breadcrumb) {
            if($breadcrumb['type'] == 'project'){
                $breadcrumbs[$index]['url'] = asset("project/".$breadcrumb['id']);
            } else if ($index == 0){
                $breadcrumbs[$index]['url'] = asset("explore");
            } else if($breadcrumb['type'] == 'container') {
                $breadcrumbs[$index]['url'] = asset('project/'.$breadcrumb['project_id'].'/container/'.$breadcrumb['id']);
            }
            array_push($extraTitle, $breadcrumb['title']);
        }

        return view('project.show', [
            'project_id' => $id,
            'extraTitle' => implode($extraTitle, ' > '),
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    public function showApi(Request $request, $projectId)
    {
        $project_repo = new ProjectRepository();
        $value_repo = new ProjectValueRepository();
        $attach_repo = new AttachmentRepository();
        $container_repo = new ContainerRepository();

        $project = $project_repo->basicInfo()
            ->with(array(
                'regions' => function($query){
                    $query->orderBy('order', 'desc');
                },
                'container' => function($query){
                    $query->with(array(
                        'indicators',
                        'form',
                    ));
                }
            ))
            ->findOrFail($projectId);

        $container_form_id = $project->container->form_id;
        $basic_form_values = array_get(
            $value_repo->getValuesOnFormsForProject($projectId, [$container_form_id]),
            $container_form_id,
            []
        );

        $speed_trackers = $value_repo->getProjectSpeedTrackers($projectId);
        $forms = $value_repo->getFormsFromExistValues($projectId, array($container_form_id));

        $slider_on_month = array_get(
            $attach_repo->getSliderOnMonths($projectId, [$container_form_id], "App\Argo\Project"),
            $container_form_id,
            []
        );
        $slider_on_month = array_values($slider_on_month);

        $attachments = $attach_repo->getPageAttachments($projectId, "App\Argo\Project");
        $subcontainers = $container_repo->getSubContainersWithCoverById($projectId, $project->container_id);

        $status_info = array(
            'created_by' => $project->created_by,
            'created_at' => (string)$project->created_at,
            'updated_at' => (string)$project->updated_at,
        );
        if(config('argodf.project_status_enabled', false))
        {
            $status_info['status'] = $project->project_status;
        }

        $map_markers = [];
        if($project->lat && $project->lng)
        {
            array_push($map_markers, array(
                'id' => $projectId,
                'type' => 'project',
                'lat' => $project->lat,
                'lng' => $project->lng
            ));
        }

        return response()->json(array(
            'title' => $project->title,
            'basic_info' => array(
                'descirption_html' => $project->description, //TODO: Change table column name
                'default_image_path' => $project->default_img_id ? argo_image_path($project->default_img_id) : null,
            ),
            'status_info' => $status_info,
            'map' => array(
                'center' => config('argodf.inner_map_center'),
                'markers' => $map_markers,
                'layers' => [],
                'tracker' => $speed_trackers
            ),
            'container_id' => $project->container_id,
            'container_name' => $project->container->name,
            'form' => array(
                'id' => $container_form_id,
                'name' => $project->container->form->name,
                'slider' => $slider_on_month,
                'values' => $basic_form_values,
            ),
            'forms' => $forms,
            'regions' => $project->regions->pluck('name'),
            'indicator_ids' => $project->container->indicators->pluck('id'),
            'subcontainers' => $subcontainers,
            'attachments' => $attachments,
        ), 200, [], JSON_NUMERIC_CHECK);
    }

    /*
    |--------------------------------------------------------------------------
    | Project Form functions
    |--------------------------------------------------------------------------
    |
    | Functions for Form sub-routes under project
    |
    */
    public function showFormApi(Request $request, $projectId, $formId)
    {
        $value_repo = new ProjectValueRepository();
        $attach_repo = new AttachmentRepository();

        $form = DynamicForm::select('name')->findOrFail($formId);
        $values = array_get(
            $value_repo->getValuesOnFormsForProject($projectId, [$formId]),
            $formId,
            []
        );
        $slider_on_month = array_get(
            $attach_repo->getSliderOnMonths($projectId, [$formId], "App\Argo\Project"),
            $formId,
            []
        );

        return response()->make(array('form' => array(
                'id' => $form->id,
                'name' => $form->name,
                'slider' => array_values($slider_on_month),
                'values' => $values
            ))
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Aggregation functions
    |--------------------------------------------------------------------------
    |
    | Functions for project's aggregated values
    |
    */
    public function aggregationIndexApi($projectId)
    {
        $aggregationService = new AggregationService($projectId);

        $containerId = Project::where('id', '=', $projectId)->value('container_id');

        $aggregations = Aggregation::where('container_id', '=', $containerId)
            ->orderBy('order')
            ->get();

        $aggregatedFields = array();
        foreach ($aggregations as $aggregation) {
            $value = $aggregationService->getAggregationValue($aggregation);
            array_push($aggregatedFields, array(
                'title' => $aggregation->title,
                'value' => $value
            ));
        }

        return response(array(
            'aggregated_fields' => $aggregatedFields
        ), 200);
    }

    /*
    |--------------------------------------------------------------------------
    | Project Report functions
    |--------------------------------------------------------------------------
    |
    | Functions for Report sub-routes under project
    |
    */
    public function reportIndex($projectId)
    {
        $project = Project::select(array('id','title'))
        ->findOrFail($projectId);

        $reports = Report::select(array(
            'id',
            'form_id',
            'project_id',
            'created_at'
        ))
        ->where('project_id', '=', $project->id)
        ->where('view_level_id', '>=', argo_current_permission())
        ->orderBy('created_at', 'DESC')
        ->with(array(
            'dynamic_form' => function ($query) {
                $query->select(array('id', 'name'));
            }
        ))
        ->get();

        $array_data = array(
            'project' => $project,
            'reports' => $reports
        );

        return response()
                ->view('report.index', $array_data);
    }

    /*
    |--------------------------------------------------------------------------
    | Project Container functions
    |--------------------------------------------------------------------------
    |
    | Functions for Container sub-routes under project
    |
    */
    public function showContainer($projectId, $containerId)
    {
        $projectRepo = new ProjectRepository();

        $project = $projectRepo->basicInfo()->findOrFail($projectId);

        $breadcrumbs = $projectRepo->getBreadcrumb($project->parent_id, [
            ['id' => $project->container_id, 'type' => 'container', 'title' => $project->container_name, 'project_id' => $project->parent_id],
            ['id' => $project->id, 'type' => 'project', 'title' => $project->title]
        ]);

        $extraTitle = array();
        foreach ($breadcrumbs as $index => $breadcrumb) {
            if($breadcrumb['type'] == 'project'){
                $breadcrumbs[$index]['url'] = asset("project/".$breadcrumb['id']);
            } else if ($index == 0){
                $breadcrumbs[$index]['url'] = asset("explore");
            }
            array_push($extraTitle, $breadcrumb['title']);
        }

        return response()->view('container.show', [
            'project_id' => $projectId,
            'breadcrumbs' => $breadcrumbs,
            'extraTitle' => implode($extraTitle, ' > '),
            'container_id' => $containerId
        ]);
    }

    public function showContainerApi($projectId, $containerId)
    {
        $container_repo = new ContainerRepository();
        $project_repo = new ProjectRepository();
        $field_repo = new FormFieldRepository();

        $project = $project_repo->basicInfo()->find($projectId);
        $container = Container::select(['id', 'name', 'reportable'])
            ->find($containerId);

        if (!$project || !$container) {
            return response()->make([], 404);
        }

        if ($container->reportable == 0) {
            $subcontainers = $container_repo->getSubContainersWithCoverById($projectId, $containerId);
        } else {
            $subcontainers = [];
        }

        $filter_fields = $field_repo->getFilterableFields([$containerId]);
        $filter_field_ids = array_pluck($filter_fields, 'id');

        $query_conditions = array(
            'parent_id' => $projectId,
            'container_id' => $containerId,
        );
        $query_order_targets = ['title'];
        $subprojects = $project_repo->getQueryProjectsWithFieldsValue($query_conditions, $query_order_targets, $filter_field_ids);

        return response()->json([
            'basic_info' => array(
                'title' => $project->title,
                'cover_image_path' => argo_image_path($project->cover_image_id),
                'default_image_path' => argo_image_path($project->default_img_id),
            ),
            'container_name' => $container->name,
            'subcontainers' => $subcontainers,
            'filters' => array(
                'field' => $filter_fields
            ),
            'subprojects' => $subprojects
        ], 200, [], JSON_NUMERIC_CHECK);
    }

    public function queryContainerApi(Request $request, $projectId, $containerId)
    {
        $project_repo = new ProjectRepository();
        $field_repo = new FormFieldRepository();

        $filter_fields = $field_repo->getFilterableFields([$containerId]);
        $filter_field_ids = array_pluck($filter_fields, 'id');

        $internal_conditions = array(
            'parent_id' => $projectId,
            'container_id' => $containerId,
        );
        $userspace_conditions = $request->input('conditions', []);
        $conditions = array_merge($internal_conditions, $userspace_conditions);

        $order_targets = array('title');
        if($request->has('order'))
        {
            array_unshift($order_targets, $request->input('order'));
        }

        $subprojects = $project_repo->getQueryProjectsWithFieldsValue($conditions, $order_targets, $filter_field_ids);

        $result = $subprojects;
        return response()->json($result, 200, [], JSON_NUMERIC_CHECK);
    }

    public function comment(Request $request, $projectId)
    {
        $array_data = array(
            'id' => $projectId,
            'type' => "App\\\\Argo\\\\Project"
        );
        return view('comment', $array_data);
    }
}
