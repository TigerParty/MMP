<?php
namespace App\Repositories;

use DB;
use Log;

use App\Services\PermissionService;
use App\Argo\Project;
use App\Argo\DynamicForm;

class ProjectRepository
{
    // Declare maximum deepth of recursive function goes
    private $maxDeepth = 10;

    /*
    |==========================================================================
    | Eloquent ORM helpers
    |==========================================================================
    | Return a Eloquent ORM object, and not yet execute the query
    | Function naming must be a noun
    */
    public function basicInfo()
    {
        return Project::select([
            'project.id',
            'project.title',
            'project.lat',
            'project.lng',
            'project.cover_image_id',
            'project.default_img_id',
            'project.project_status_id',
            'project.container_id',
            'project.parent_id',  // Necessary for controller to fetch breadcrumb
            'project.description',
            'user.name AS created_by',
            'project.created_at',
            'project.updated_at',
            'project_status.name AS project_status',
            'project.view_level_id',
        ])
            ->leftJoin('user', 'project.created_by', '=', 'user.id')
            ->leftJoin('project_status', 'project.project_status_id', '=', 'project_status.id');
    }

    /*
    |--------------------------------------------------------------------------
    | projectsWithFieldsValue()
    |--------------------------------------------------------------------------
    | Condition keys:
    |   parent_id: 1 (default to null)
    |   container_id: 1
    |   title: "key1 key2 key3" (Search in fuzzy)
    |   field: {"id": 1, "filter_key" "drop_down_list" "value": "some keyword"}
    |   region_ids: [1, 2, 3]
    */
    public function projectsWithFieldsValue($conditions, $orderTargets, $targetFieldIds, $select = false)
    {
        if (!$select) {
            $select = [
                'project.id',
                'project.title',
                'project.lat',
                'project.lng',
                'project.cover_image_id',
                'project.created_at',
                'project.updated_at'
            ];
        }

        $orm = Project::select($select)
            ->with(['values' => function ($query) use ($targetFieldIds) {
                $query->whereIn('form_field_id', $targetFieldIds);
            }]);

        if (array_has($conditions, 'parent_id')) {
            $orm->where('parent_id', '=', array_get($conditions, 'parent_id'));
        } else {
            // Return root projects by default
            $orm->whereNull('parent_id');
        }

        if (array_has($conditions, 'container_id')) {
            $orm->where('container_id', array_get($conditions, 'container_id'));
        }

        if (array_has($conditions, 'title')) {
            $titleKeywords = explode(' ', array_get($conditions, 'title', ''));
            foreach ($titleKeywords as $titleKeyword) {
                $orm->whereIn('id', (function ($query) use ($titleKeyword) {
                    $query->select('id')
                        ->from(with(new Project)->getTable());
                    $query->orWhere('title', 'like', "%$titleKeyword%");
                }));
            }
        }

        if (array_has($conditions, 'fields')) {
            $filter_fields = array_get($conditions, 'fields');
            foreach ($filter_fields as $filter_field) {
                $orm->whereIn('id', function ($query) use ($filter_field) {
                    $filter_type = array_get($filter_field, 'filter_key');
                    $filter_value = array_get($filter_field, 'value', '');
                    $field_id = array_get($filter_field, 'id', '');

                    $query->select('project_id')
                        ->from('project_value')
                        ->where('form_field_id', '=', $field_id);

                    if ($filter_type == 'drop_down_list') {
                        $query->where('value', '=', $filter_value);
                    } elseif ($filter_type == 'check_box') {
                        // Workaround for check_box value saved in different format
                        $query->where(function ($subQuery) use ($filter_value) {
                            $subQuery->orWhere('value', '=', $filter_value)
                                ->orWhere('value', '=', 'yes');
                        });
                    } elseif ($filter_type == 'text_box') {
                        $query->where(function ($subQuery) use ($filter_value) {
                            $valueKeywords = explode(' ', $filter_value);
                            foreach ($valueKeywords as $valueKeyword) {
                                $subQuery->orWhere('value', 'LIKE', "%$valueKeyword%");
                            }
                        });
                    } else {
                        $query->where('value', 'LIKE', '%'.$filter_value.'%');
                    }
                });
            }
        }

        if (array_has($conditions, 'region_ids')) {
            $regionIds = array_get($conditions, 'region_ids');
            foreach ($regionIds as $regionId) {
                $orm->whereHas('regions', function ($query) use ($regionId) {
                    $query->where('id', '=', $regionId);
                });
            }
        }

        if (array_has($conditions, 'status')) {
            $filterStatus = array_get($conditions, 'status');
            $orm->where('project_status_id', '=', $filterStatus['id']);
        }

        // TODO: implement categories filter by whereIn
        // if(array_has($conditions, 'categories'))
        // {
        // }

        // Order on targets
        foreach ($orderTargets as $orderTarget) {
            if ($orderTarget == 'title') {
                $orm->orderBy('title', 'ASC');
            } elseif ($orderTarget == 'created_at') {
                $orm->orderBy('created_at', 'DESC');
            } elseif ($orderTarget == 'updated_at') {
                $orm->orderBy('updated_at', 'DESC');
            } elseif (starts_with($orderTarget, 'field')) {
                $orderObj = explode('.', $orderTarget);

                $joinAlias = "$orderObj[0]$orderObj[1]";
                $fieldId = (int)$orderObj[1];

                $orm->leftJoin("project_value AS $joinAlias", function ($join) use ($joinAlias, $fieldId) {
                    $join->on(
                        "$joinAlias.project_id",
                        '=',
                        'project.id'
                    )->on(
                        "$joinAlias.form_field_id",
                        '=',
                        DB::raw(app('db')->getPdo()->quote($fieldId))
                    );
                })->orderBy("$joinAlias.value", 'ASC');
            } //TODO: Implement category sorter here
            // elseif(starts_with($orderTarget, 'category')) {}

            //TODO: Implement region sorter here
            // elseif(starts_with($orderTarget, 'region')) {}
            else {
                Log::warning("Got unknown orderTarget in ProjectRepository@getQueryProjectsWithFieldsValue: $orderTarget");
            }
        }

        return $orm;
    }

    /*
    |==========================================================================
    | Complex query executor
    |==========================================================================
    | Return a finished query result
    | Function naming must start with 'get'
    */

    public function getQueryProjectsWithFieldsValue($conditions, $orderTargets, $targetFieldIds, $select = false)
    {
        $orm = $this->projectsWithFieldsValue($conditions, $orderTargets, $targetFieldIds, $select);

        // Generate pathes and remove unused keys
        $subprojects = $orm->get();
        foreach ($subprojects as $subproject) {
            $subproject->cover_image_path = argo_image_path($subproject->cover_image_id);
            $subproject->path = url("/project/$subproject->id");

            unset($subproject->cover_image_id);

            $arrangedValues = [];
            foreach ($subproject->values as $value) {
                $arrangedValues[$value->form_field_id] = $value;
            }
            unset($subproject->values);
            $subproject->values = $arrangedValues;
        }

        return $subprojects;
    }

    public function getWithValueOnFields($fieldIds)
    {
        return Project::select([
            'id',
            'title',
            'description',
        ])
            ->with(['values' => function ($query) use ($fieldIds) {
                $query->whereIn('form_field_id', $fieldIds);
            }]);
    }

    public function getBreadcrumb($projectId, $breadcrumbs = [], $deepth = 0)
    {
        if ($deepth > $this->maxDeepth) {
            \Log::warning("BreadcrumbRepository: Recursive over $this->maxDeepth level in getBreadCrumb, break recursive.");
            return $breadcrumbs;
        }

        $project = Project::select([
            'project.id',
            'project.title',
            'project.parent_id',
            'project.container_id',
            'container.name AS container_name'
        ])
            ->leftJoin('container', 'container.id', '=', 'project.container_id')
            ->where('project.id', '=', $projectId)
            ->first();

        if (!$project) {
            // EOF recursive, return
            return $breadcrumbs;
        }

        array_unshift($breadcrumbs, [
            'id' => $project->id,
            'type' => 'project',
            'title' => $project->title
        ]);

        if ($project->container_id) {
            array_unshift($breadcrumbs, [
                'id' => $project->container_id,
                'project_id' => $project->parent_id,
                'type' => 'container',
                'title' => $project->container_name
            ]);
        }

        if ($project->parent_id) {
            // Still has parent, go deep
            return $this->getBreadcrumb(
                $project->parent_id,
                $breadcrumbs,
                $deepth+1
            );
        }

        return $breadcrumbs;
    }

    public function getBatchExcelProject($parentId, $containerId, $formId)
    {
        $projectOrm = Project::select([
            'project.id',
            'project.title',
            'project.lat',
            'project.lng'
        ])
            ->with([
                'regions' => function ($query) {
                    $query->select([
                        'label_name',
                        'name'
                    ])
                        ->orderBy('order', 'ASC');
                }
            ])
            ->where('project.container_id', '=', $containerId)
            ->where('project.parent_id', '=', $parentId)
            ->whereNull('project.deleted_at')
            ->orderBy('project.title', 'ASC');

        // filter out root project with permission
        if (empty($parentId)) {
            $projectOrm->where(function ($query) {
                $query->whereHas('edit_level', function ($subQuery) {
                    $subQuery->where('priority', '>=', argo_current_permission());
                });
                $query->OrWhereHas('owners', function ($subQuery) {
                    $subQuery->where('user_id', session('user_id'));
                });
            });
        }

        return $projectOrm->get();
    }
}
