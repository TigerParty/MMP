<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Argo\Container;
use App\Argo\Region;
use App\Argo\FormField;
use App\Argo\ProjectStatus;

use App\Repositories\ProjectRepository;

use App\Services\RegionService;
use App\Services\ProjectValueService;

class ExploreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rootContainer = Container::find(config('argodf.default_container_id'));

        $statuses = ProjectStatus::get(['id', 'name']);

        $array_data = array(
            'root_container' => $rootContainer,
            'statuses' => $statuses,
        );

        return view('explore.index', $array_data);
    }

    public function queryApi(Request $request)
    {
        $projectRepo = new ProjectRepository();
        $regionService = new RegionService();
        $valueService = new ProjectValueService();

        $formFields = $request->input('fields', []);

        $formFieldIds = [];
        foreach($formFields as $formField) {
            array_push($formFieldIds, $formField['id']);
        }

        // Binding filter conditions
        $conditions = [
            'container_id' => config('argodf.default_container_id'),
        ];

        $filterKeyword = $request->input('conditions.keyword', false);
        if ($filterKeyword) {
            $conditions['title'] = $filterKeyword;
        }

        $filterRegionIds = $request->input('conditions.regions', []);
        if ($filterRegionIds) {
            $conditions['region_ids'] = $filterRegionIds;
        }

        $filterFields = $request->input('conditions.fields', []);
        if ($filterFields) {
            $conditions['fields'] = $filterFields;
        }

        $filterStatus = $request->input('conditions.status', []);
        if ($filterStatus) {
            $conditions['status'] = $filterStatus;
        }

        $orderBy = $request->input('order_by', ['title']);

        // Initial query ORM, and exectute 2 query for page items and count
        $projectOrm = $projectRepo->projectsWithFieldsValue(
            $conditions,
            $orderBy,
            $formFieldIds,
            ['id', 'title', 'lat', 'lng', 'description', 'cover_image_id', 'updated_at']
        );

        $projects = $projectOrm
            ->with([
                'regions' => function($query) {
                    $query->select(['id', 'name', 'label_name']);
                },
                'values' => function($query) use ($formFieldIds) {
                    $query->whereIn('form_field_id', $formFieldIds);
                }
            ])
            ->where('view_level_id', '>=', argo_current_permission())
            ->get();

        $requireDecodeFieldIds = FormField::select('id')
            ->whereIn('field_template_id', [9])
            ->get();

        // Arrange project regions/values into groups
        foreach($projects as $project) {
            $arrangedRegions = $regionService->arrangeRegionKeyByLabel($project->regions);
            unset($project->regions);
            $project->regions = $arrangedRegions;

            $arrangedValues = $valueService->arrangeValueKeyByFieldId($project->values, array_pluck($requireDecodeFieldIds, 'id'));
            unset($project->values);
            $project->values = $arrangedValues;

            $project->cover_image_path = argo_image_path($project->cover_image_id, asset('/images/default_thumb_explore.png'));
        }

        // Make response
        return response()->json([
            'form_fields' => $formFields,
            'projects' => $projects,
        ], 200, [], JSON_NUMERIC_CHECK);
    }
}
