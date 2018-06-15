<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\Argo\Project;
use App\Argo\FormField;
use App\Argo\RegionLabel;
use App\Argo\ProjectStatus;

class ProjectController extends Controller
{
    const ITEMS_PER_PAGE = 30;
    const HOURS_AS_NEW_PROJECT = 24;

    private $approved_perm;
    private $unapproved_perm;

    public function __construct()
    {
        $this->approved_perm = config('argodf.default_view_priority');
        $this->unapproved_perm = config('argodf.default_perm.project.edit');
    }

    public function query(Request $request)
    {
        $request->validate([
            'region_ids'   => 'array',
            'container_id' => 'exists:container,id',
            'status_id'    => 'exists:project_status,id',
            'keyword'      => 'string|nullable',
            'values'       => 'array',
            'page'         => 'numeric',
        ]);

        $userId = session('user_id');
        $userPerm = argo_current_permission();

        // Project Query Builder
        $projectOrm = Project::selectRaw('
                project.id,
                project.title,
                project.project_status_id AS status_id,
                project.updated_at,
                project.container_id,
                project.cover_image_id,
                project.view_level_id
            ')
            ->with(['regions' => function($query){
                $query->select(['id', 'name', 'label_name']);
            }])
            ->whereNull('project.parent_id')
            ->where(function($query) use ($userPerm, $userId){
                $query->whereHas('edit_level', function($subQuery) use ($userPerm){
                    $subQuery->where('priority', '>=', $userPerm);
                });
                $query->orWhereHas('owners', function($subQuery) use ($userId){
                    $subQuery->where('user_id', $userId);
                });
            });

        if($request->has('keyword'))
        {
            $keywords = explode(" ", $request->get('keyword'));
            foreach($keywords as $keyword)
            {
                $projectOrm->where('project.title', 'like', "%$keyword%");
            }
        }

        if($request->has('status_id'))
        {
            $projectOrm->where('project.project_status_id', '=', $request->get('status_id'));
        }

        if($request->has('region_ids'))
        {
            $regionIds = $request->get('region_ids');
            foreach($regionIds as $regionId)
            {
                $projectOrm->whereIn('id', function($query) use ($regionId){
                    $query->select('project_id')
                        ->from('relation_project_belongs_region')
                        ->where('region_id', '=' , $regionId);
                });
            }
        }

        if($request->has('values'))
        {
            $valuesOnFieldId = $request->get('values');

            $filteringFields = FormField::selectRaw('
                    form_field.id,
                    field_template.filter_key AS `template`
                ')
                ->leftJoin('field_template', 'field_template.id', '=', 'form_field.field_template_id')
                ->whereIn('form_field.id', array_keys($valuesOnFieldId))
                ->get();

            foreach($filteringFields as $filteringField)
            {
                $projectOrm->whereIn('id', function($query) use ($filteringField, $valuesOnFieldId){
                    $fieldId = $filteringField->id;
                    $template = $filteringField->template;
                    $value = $valuesOnFieldId[$fieldId];

                    $query->select('project_id')
                        ->from('project_value')
                        ->where('form_field_id', '=', $fieldId);

                    if($template == 'text_box')
                    {
                        $keywordsInValue = explode(' ', $value);
                        foreach($keywordsInValue as $keywordInValue)
                        {
                            $query->where('value', 'like', "%$keywordInValue%");
                        }
                    }
                    else
                    {
                        // drop_down_list, numerical
                        $query->where('value', '=', $value);
                    }
                });
            }
        }

        // Project Total Count
        $projectCountOrm = clone $projectOrm;
        $projectCount = $projectCountOrm->count();

        // New Project Count
        $newProjectCountOrm = clone $projectOrm;
        $newProjectCount = $newProjectCountOrm
            ->where('updated_at', '>', Carbon::now(config('app.timezone'))->subHours(self::HOURS_AS_NEW_PROJECT))
            ->count();

        // Project List of the page
        $offset = ((int)$request->get('page', 0)) * self::ITEMS_PER_PAGE;

        $projects = $projectOrm
            ->limit(self::ITEMS_PER_PAGE)
            ->offset($offset)
            ->get();

        // Project Serializing
        foreach($projects as $index => $project)
        {
            // Region list
            $regionOnLabel = [];
            foreach($project->regions as $region)
            {
                $regionOnLabel[$region->label_name] = $region->name;
            }
            unset($projects[$index]->regions);
            $projects[$index]->regions = $regionOnLabel;

            // Cover Image
            $coverImageUrl = ($project->cover_image_id) ?
                asset("/file/$project->cover_image_id") :
                null;
            unset($projects[$index]->cover_image_id);
            $projects[$index]->cover_image = $coverImageUrl;

            // New Project Flag
            $hoursAfterUpdated = Carbon::now(config('app.timezone'))
                ->diffInHours($project->updated_at);
            $project->is_new = $hoursAfterUpdated < self::HOURS_AS_NEW_PROJECT;

            // Approval Flag
            $approvalFlag = $project->view_level_id == $this->approved_perm;
            unset($project->view_level_id);
            $project->is_approved = $approvalFlag;
        }

        // Field Filters for the Container
        $containerId = ($request->has('container_id')) ?
            $request->get('container_id') :
            config('argodf.default_container_id');

        $filters = FormField::selectRaw('
                form_field.id,
                form_field.name,
                form_field.options,
                field_template.filter_key AS `template`
            ')
            ->leftJoin('field_template', 'field_template.id', '=', 'form_field.field_template_id')
            ->whereIn('form_field.id', function($query) use ($containerId) {
                $query->from('relation_field_filter_container')
                    ->where('container_id', '=', $containerId)
                    ->select('form_field_id');
            })
            ->get();

        // Region Label List
        $labels = RegionLabel::select([
                'name'
            ])
            ->orderBy('order', 'asc')
            ->get()
            ->pluck('name');

        // Status List
        $status = ProjectStatus::select([
                'id',
                'name',
            ])
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'project' => $projects,
            'project_count' => $projectCount,
            'project_count_new' => $newProjectCount,
            'region_label' => $labels,
            'status' => $status,
            'filter' => $filters,
            'items_per_page' => self::ITEMS_PER_PAGE,
        ], 200);
    }

    public function updateApproval(Request $request, $id)
    {
        $request->validate([
            'approved' => 'required|boolean',
        ]);

        $project = Project::select(['id', 'view_level_id'])
            ->findOrFail($id);
        $isApproved = $request->get('approved');

        $project->view_level_id = ($isApproved) ?
            $this->approved_perm :
            $this->unapproved_perm;
        $project->save();

        $userId = session('user_id', 'UNKNOW');
        info("Project ($id) view_level_id update to $project->view_level_id since user ($userId) set approval to $isApproved");

        return response()->json([
            'status' => 'Ok'
        ], 200);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_id' => 'exists:project_status,id',
        ]);

        $project = Project::select(['id', 'project_status_id'])
            ->findOrFail($id);

        if($project->project_status_id == $request->get('status_id'))
        {
            return response()->json([
                'status' => 'Accepted but not modified'
            ], 202);
        }

        $project->project_status_id = $request->get('status_id');
        $project->save();

        return response()->json([
            'status' => 'Ok'
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $project = Project::findOrFail($id)->delete();
        info("Project ($id) deleted by user (".session()->get('user_id').")");

        return response()->json([
            'status' => 'Ok'
        ], 200);
    }
}
