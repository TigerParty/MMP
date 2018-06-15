<?php

namespace App\Http\Controllers;

use Log;
use Excel;
use Validator;
use Auth;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\Argo\Project;
use App\Argo\ProjectValue;
use App\Argo\Container;
use App\Argo\DynamicForm;
use App\Argo\FormField;
use App\Argo\PermissionLevel;
use App\Argo\Region;
use App\Argo\RegionLabel;
use App\Argo\ProjectStatus;

use App\Repositories\ProjectRepository;
use App\Repositories\ProjectValueRepository;
use App\Repositories\AttachmentRepository;
use App\Repositories\ContainerRepository;
use App\Repositories\FormFieldRepository;

use App\Services\RegionService;
use App\Services\ProjectValueService;
use App\Services\NotificationService;

use App\Events\ProjectValueUpdated;

class ProjectAdminController extends Controller
{
    const DATA_SHEET_TITLE = 'data';
    const PROTECT_SHEET_TITLE = 'protect';
    const SHEET_PWD = 'goargogo';

    private $itemsPerPage = 50;

    public function index()
    {
        $rootContainerId = config('argodf.default_container_id');
        $container = Container::select(['name', 'form_id'])
            ->findOrFail($rootContainerId);

        $projectCount = Project::select('id')
            ->where('container_id', '=', $rootContainerId)
            ->count('id');

        return view('admin.project.index', [
            // Parameters for FE init_data, need to careful if it's a string
            'container_id' => $rootContainerId,
            'container_form_id' => $container->form_id,
            'items_per_page' => $this->itemsPerPage,
            // In-page Blade print
            'container_name' => $container->name,
        ]);
    }

    public function indexQueryApi(Request $request)
    {
        $projectRepo = new ProjectRepository();
        $fieldRepo = new FormFieldRepository();
        $regionService = new RegionService();
        $valueService = new ProjectValueService();

        $limit = $request->input('limit', $this->itemsPerPage);
        if ($limit > $this->itemsPerPage) {
            Log::info("Try to fetch $limit rows a time in AdminProjectIndex, limited it to $this->itemsPerPage");
            $limit = $this->itemsPerPage;
        }

        $offset = (int)$request->input('page', 0) * $limit;

        $formId = $request->input('conditions.form');
        $formFields = FormField::select([
                'form_field.id',
                'form_field.name',
                'form_field.options',
                'field_template.key',
                'form_field.formula'
            ])
            ->leftJoin('field_template', 'form_field.field_template_id', '=', 'field_template.id')
            ->where('form_field.edit_level_id', '>=', argo_current_permission())
            ->where('form_field.form_id', '=', $formId)
            ->where('field_template.key', '!=', 'gps_tracker') // quick fix for hidden tracker data in inline editor
            ->orderBy('form_field.order')
            ->get();

        $formFieldIds = [];
        foreach ($formFields as $formField) {
            array_push($formFieldIds, $formField->id);
            $formField->options = $formField->options;
        }

        // Binding filter conditions
        $containerId = $request->input('conditions.container_id', config('argodf.default_container_id'));
        $conditions = [
            'container_id' => $containerId,
        ];

        $parentId = $request->input('conditions.parent_id', null);
        if ($parentId != null) {
            $conditions['parent_id'] = $parentId;
        }

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

        $orderBy = $request->input('order_by', ['title']);

        // Initial query ORM, and exectute 2 query for page items and count
        $projectOrm = $projectRepo->projectsWithFieldsValue(
            $conditions,
            $orderBy,
            $formFieldIds,
            ['id', 'title']
        );

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

        $projectCounterOrm = clone $projectOrm;
        $projectCount = $projectCounterOrm->count();

        $projects = $projectOrm
            ->with([
                'regions' => function ($query) {
                    $query->select(['id', 'name', 'label_name']);
                },
                'values' => function ($query) use ($formFieldIds) {
                    $query->whereIn('form_field_id', $formFieldIds);
                }
            ])
            ->offset($offset)
            ->limit($limit)
            ->get();
        $requireDecodeFieldIds = FormField::select('id')
            ->whereIn('field_template_id', [9])
            ->get();

        // Arrange project regions/values into groups
        foreach ($projects as $project) {
            $arrangedRegions = $regionService->arrangeRegionKeyByLabel($project->regions);
            unset($project->regions);
            $project->regions = $arrangedRegions;

            $arrangedValues = $valueService->arrangeValueKeyByFieldId($project->values, array_pluck($requireDecodeFieldIds, 'id'));
            unset($project->values);
            $project->values = $arrangedValues;
        }

        // Make response
        return response()->json([
            'form_fields' => $formFields,
            'page_projects' => $projects,
            'projects_count' => $projectCount,
        ], 200, [], JSON_NUMERIC_CHECK);
    }

    public function create(Request $request, $parentId = null, $containerId = null)
    {
        return view('admin.project.create', [
            'parent_id' => $parentId,
            'container_id' => $containerId,
        ]);
    }

    public function createApi(Request $request, $parentId = null, $containerId = null)
    {
        if (!$containerId) {
            $containerId = config('argodf.default_container_id');
        }

        $projectStatuses = ProjectStatus::select(['id', 'name', 'default'])
            ->get();
        $permissionLevels = PermissionLevel::where('priority', '>=', argo_current_permission())
            ->orderBy('priority')
            ->get(['id', 'name']);

        $responseArray = [
            'permission_levels' => $permissionLevels,
            'project' => [
                'parent_id' => $parentId,
                'container_id' => $containerId,
                'view_level_id' => config('argodf.default_perm.project.view'),
                'edit_level_id' => config('argodf.default_perm.project.edit'),
            ],
            'fields' => [],
            'statuses' => $projectStatuses,
        ];

        // Generate editable fields & container info
        $fieldRepo = new FormFieldRepository();
        $container = Container::where('id', '=', $containerId)->first();

        if ($container) {
            $responseArray['container'] = $container;

            $responseArray['fields'] = array_get(
                $fieldRepo->getFieldsWithTemplate([$container->form_id], true),
                $container->form_id,
                []
            );
        }

        // Make only root projects has ability to modify with regions
        if (!$parentId) {
            $regionLabels = RegionLabel::orderBy('order')
                ->get()
                ->pluck('name');

            $responseArray['region_labels'] = $regionLabels;
            $responseArray['breadcrumbs'] = [
                ['id' => $containerId, 'type' => 'container', 'title' => $container->name],
            ];
        } else {
            $projectRepo = new ProjectRepository();
            $responseArray['breadcrumbs'] = $projectRepo->getBreadcrumb($parentId);
        }

        return response()->json($responseArray, 200, [], JSON_NUMERIC_CHECK);
    }

    public function store(Request $request)
    {
        $containerId = $request->has('project.container_id') ? $request->input('project.container_id') : config('argodf.default_container_id');

        $container = Container::select([
                'id',
                'name',
                'title_duplicatable',
            ])
            ->find($containerId);

        $rules = [
            'project.lat' => 'nullable|numeric',
            'project.lng' => 'nullable|numeric',
            'project.regions' => 'array',
            'project.fields' => 'array',
            'project.attach_ids' => 'array',
        ];

        if ($container->title_duplicatable == 1) {
            $rules['project.title'] = 'required';
        } else {
            $rules['project.title'] = 'required|unique:project,title';
        }

        $this->validate(
            $request,
            $rules,
            [
                'project.title.required' => 'Name is required',
                'project.title.unique' => 'The '.$container->name.' name has been taken.',
            ]
        );

        $parentId = $request->has('project.parent_id') ? $request->input('project.parent_id') : null;

        $project = new Project();
        $project->title = $request->input('project.title');
        $project->parent_id = $parentId;
        if ($request->has('project.lat') and $request->has('project.lng')) {
            $project->lat = $request->input('project.lat');
            $project->lng = $request->input('project.lng');
        }

        $project->default_img_id = $request->input('project.default_img_id', null);

        $project->cover_image_id = $request->input('project.cover_image_id', null);

        if ($request->input('project.updated_at', null)) {
            $project->updated_at = new Carbon($request->input('project.updated_at'), config('app.timezone'));
        }

        if (count(config('argodf.group_id')) == 1) {
            $project->group_id = config('argodf.group_id')[0];
        }

        $project->container_id = $containerId;
        $project->project_status_id = $request->input('project.status_id', null);

        $viewLevelId = $request->input('project.view_level_id', null);
        $project->view_level_id = ($viewLevelId) ? $viewLevelId : config('argodf.default_perm.project.view');

        $editLevelId = $request->input('project.edit_level_id', null);
        $project->edit_level_id = ($editLevelId) ? $editLevelId : config('argodf.default_perm.project.edit');

        $project->save();

        $regionIds = $request->input('project.regions');
        if ($regionIds) {
            $project->regions()->sync($regionIds);
        }

        $attachIds = $request->input('project.attach_ids');
        if ($attachIds) {
            $project->attachments()->sync($attachIds);
        }

        $rawValues = $request->input('project.values', []);
        $values = [];
        foreach ($rawValues as $fieldId => $fieldValue) {
            if (is_array($fieldValue)) {
                $checkBoxGroupValues = array();
                foreach ($fieldValue as $key => $value) {
                    array_push($checkBoxGroupValues, $key);
                }
                $fieldValue = json_encode($checkBoxGroupValues);
            }
            array_push($values, [
                'project_id' => $project->id,
                'form_field_id' => $fieldId,
                'value' => $fieldValue
            ]);
        }
        ProjectValue::insert($values);

        event(new ProjectValueUpdated($project->id));

        return response()->make([
            'id' => $project->id,
            // FE will redirect user to this url path
            'path' => asset('/admin/project/'.$project->id),
        ]);
    }

    public function show($projectId)
    {
        $project = Project::select(array(
            'id', 'container_id'
        ))
        ->with(array('container' => function ($query) {
            $query->select(array(
                'container.id',
                'container.card_rule',
            ));
        }))
        ->find($projectId);

        return view('admin.project.show', [
            'projectId' => $projectId,
            'cardRule' => $project->container->card_rule,
        ]);
    }

    public function showApi($projectId)
    {
        $projectRepo = new ProjectRepository();
        $valueRepo = new ProjectValueRepository();
        $attachRepo = new AttachmentRepository();
        $containerRepo = new ContainerRepository();

        //Basic Info Section
        $project = $projectRepo->basicInfo()
            ->with([
                'regions' => function ($query) {
                    $query->select(['region.name', 'region.label_name'])
                        ->leftJoin('region_label', 'region_label.name', '=', 'region.label_name')
                        ->orderBy('region_label.order');
                },
                'container'
            ])
            ->findOrFail($projectId);

        $attachments = $attachRepo->getPageAttachments($projectId, "App\Argo\Project");

        //Forms Section
        $existingForms = $valueRepo->getFormsFromExistValues($projectId);

        $existingFormValues = $valueRepo->getValuesOnFormsForProject($projectId, array_pluck($existingForms, 'id'), true);

        $existingFormMediaGroups = $attachRepo->getSliderOnMonths($projectId, array_pluck($existingForms, 'id'), "App\Argo\Project");

        foreach ($existingForms as $form) {
            $form->values = array_get(
                $existingFormValues,
                $form->id,
                []
            );

            foreach ($form->values as $formValue) {
                if ($formValue->field_template_key == "gps_tracker") {
                    $formValue->value = json_decode($formValue->value);
                }
            };

            $form->media_groups = array_values(array_get(
                $existingFormMediaGroups,
                $form->id,
                []
            ));
        }

        // Recursive run leaf subcontainers without internal nodes
        $flatSubContainers = $containerRepo->getFlattenSubContainers($project->container_id);

        // Recursive fetching breadcrumbs
        // Pass self project as initial value to prevent recursive run same query again
        $breadcrumbs = $projectRepo->getBreadcrumb($project->parent_id, [
            ['id' => $project->container_id, 'type' => 'container', 'title' => $project->container->name],
            ['id' => $project->id, 'type' => 'project', 'title' => $project->title]
        ]);

        return response()->make(array(
            'id' => $project->id,
            'title' => $project->title,
            'basic_info' => array(
                'regions' => $project->regions,
                'descirption_html' => $project->description, //TODO: Change table column name
                'default_image_path' => argo_image_path($project->default_img_id, config('argodf.default_project_logo')),
            ),
            'forms' => $existingForms,
            'subcontainers' => $flatSubContainers,
            'breadcrumbs' => $breadcrumbs,
        ));
    }

    public function showCard($projectId)
    {
        $responseData = array();
        $parentProject = array();
        $projectValue = array();

        $project = Project::select(array(
            'id', 'title', 'container_id',
            'default_img_id', 'cover_image_id',
            'parent_id', 'uid',
        ))
        ->with(array(
            'container' => function ($query) {
                $query->select(array(
                    'container.id',
                    'container.card_rule',
                    'container.name',
                ));
            },
        ))
        ->findOrFail($projectId);

        if ($project->parent_id) {
            $parentProject = Project::select(array(
                'id', 'title',
                'container_id',
                'default_img_id',
            ))
            ->with(array(
                'container' => function ($query) {
                    $query->select(array(
                        'container.id',
                        'container.name',
                    ));
                },
            ))
            ->find($project->parent_id);
        }

        $cardRule = $project->container->card_rule;
        $cardFields = array_get($cardRule, 'fields', array());
        if ($cardFields) {
            $projectValue = FormField::select(array(
                'form_field.id',
                'form_field.name',
                'project_value.value',
                'project_value.project_id',
                'project_value.form_field_id',
            ))
            ->leftJoin('project_value', function ($join) use ($projectId) {
                $join->on('form_field.id', '=', 'project_value.form_field_id')
                ->where('project_value.project_id', '=', $projectId);
            })
            ->whereIn('form_field.id', $cardFields)
            ->orderByRaw('FIELD(form_field_id,' .implode(',', $cardFields). ')')
            ->get();
        }

        $responseData = array(
            'project' => $project,
            'parentProject' => $parentProject,
            'projectValue' => $projectValue,
        );

        return response()->view('admin.project.card', $responseData);
    }

    public function edit($projectId)
    {
        return view('admin.project.edit', [
            'project_id' => $projectId,
        ]);
    }

    public function editApi($projectId)
    {
        $fieldRepo = new FormFieldRepository();
        $valueRepo = new ProjectValueRepository();
        $projectRepo = new ProjectRepository();

        $project = Project::select([
                'id',
                'title',
                'lat',
                'lng',
                'description',
                'project_status_id',
                'default_img_id',
                'cover_image_id',
                'created_at',
                'updated_at',
                'container_id',
                'view_level_id',
                'edit_level_id',
            ])
            ->with([
                'container',
                'attaches' => function ($query) {
                    $query->whereNull('attached_form_id');
                },
                'regions' => function ($query) {
                    $query->select(['id', 'name'])->get();
                }
            ])
            ->findOrFail($projectId);

        $projectStatuses = ProjectStatus::select(['id', 'name', 'default'])
            ->get();
        $permissionLevels = PermissionLevel::where('priority', '>=', argo_current_permission())
            ->orderBy('priority')
            ->get(['id', 'name']);

        $projectAttaches = [];
        $attachesRaw = $project->attaches;
        foreach ($attachesRaw as $attach) {
            array_push($projectAttaches, [
                'id' => $attach->id,
                'name' => $attach->name,
                'download_path' => asset("/file/$attach->id/download"),
            ]);
        }

        $breadcrumbs = $projectRepo->getBreadcrumb($projectId, []);

        $regionLabels = RegionLabel::orderBy('order')
                ->get()
                ->pluck('name');

        $responseArray = [
            'permission_levels' => $permissionLevels,
            'project' => [
                'id' => $project->id,
                'title' => $project->title,
                'lat' => $project->lat,
                'lng' => $project->lng,
                'status_id' => $project->project_status_id,
                'default_img_id' => $project->default_img_id,
                'cover_image_id' => $project->cover_image_id,
                'created_at' => $project->created_at->toAtomString(),
                'updated_at' => $project->updated_at->toAtomString(),
                'container_id' => $project->container_id,
                'view_level_id' => $project->view_level_id,
                'edit_level_id' => $project->edit_level_id,
                'attaches' => $projectAttaches,
                'regions' => $project->regions->pluck('id'),
            ],
            'statuses' => $projectStatuses,
            'breadcrumbs' => $breadcrumbs,
            'region_labels' => $regionLabels,
        ];

        if ($project->container) {
            $responseArray['container'] = $project->container;

            $formId = $project->container->form_id;
        } else {
            Log::warning("Project $projectId has empty container_id column.");
        }

        return response()->json($responseArray, 200, [], JSON_NUMERIC_CHECK);
    }

    public function update(Request $request, $projectId)
    {
        $this->validate(
            $request,
            [
                'project.title' => 'required',
                'project.lat' => 'nullable|numeric',
                'project.lng' => 'nullable|numeric',
                'project.regions' => 'array',
                'project.fields' => 'array',
                'project.attach_ids' => 'array',
            ],
            [
                'project.title.required' => 'Name is required',
            ]
        );

        $project = Project::select('id')
            ->findOrFail($projectId);

        try {
            \DB::beginTransaction();

            $project->title = $request->input('project.title');
            if ($request->has('project.lat') and $request->has('project.lng')) {
                $project->lat = $request->input('project.lat');
                $project->lng = $request->input('project.lng');
            }

            $project->default_img_id = $request->input('project.default_img_id', null);

            $project->cover_image_id = $request->input('project.cover_image_id', null);

            if ($request->input('project.updated_at', null)) {
                $project->updated_at = new Carbon($request->input('project.updated_at'), config('app.timezone'));
            }

            $project->project_status_id = $request->input('project.status_id', null);

            $viewLevelId = $request->input('project.view_level_id', null);
            $project->view_level_id = ($viewLevelId) ? $viewLevelId : config('argodf.default_perm.project.view');

            $editLevelId = $request->input('project.edit_level_id', null);
            $project->edit_level_id = ($editLevelId) ? $editLevelId : config('argodf.default_perm.project.edit');

            $project->save();

            // Update Regions by Eloquent sync()
            $regionIds = $request->input('project.regions');
            if ($regionIds) {
                $project->regions()->sync($regionIds);
            }

            \DB::table('attachables')->where('attachable_id', $projectId)
                ->where('attachable_type', '=', 'App\Argo\Project')
                ->whereNull('attached_form_id')
                ->delete();
            // Update Attachments by Eloquent sync()
            $attachIds = array();
            foreach ($request->input('project.attach_ids') as $attachId) {
                $attachIds[$attachId] = ['attached_form_id' => null];
            }
            $project->attachments()->attach($attachIds);
            $project->touch();

            $updater = auth()->user();
            Log::debug("Project $projectId updated by user $updater->name ($updater->id) from web.");

            $notificationService = new NotificationService();
            $notificationService->sendEntityUpdatedNotification(
                'App\Argo\Project',
                $projectId
            );

            \DB::commit();

            return response()->make($project);
        } catch (Exception $e) {
            \DB::rollback();
            \Log::error($e);
            abort(400);
        }
    }

    public function formCreate($projectId, $formId)
    {
        $responseArray = [
            'projectId' => $projectId,
            'formId' => $formId
        ];

        $validator = Validator::make($responseArray, [
            'projectId' => 'exists:project,id,deleted_at,NULL',
            'formId' => 'exists:dynamic_form,id,deleted_at,NULL'
        ]);

        if ($validator->fails()) {
            abort(404);
        }

        return view('admin.project.form_create', [
            'projectId' => $projectId,
            'formId' => $formId
        ]);
    }

    public function formCreateApi($projectId, $formId)
    {
        $fieldRepo = new FormFieldRepository();
        $projectRepo = new ProjectRepository();

        $project = Project::select([
                'project.title',
                'project.parent_id',
                'container.name AS container_name',
                'container.id AS container_id',
            ])
            ->leftJoin('container', 'container.id', '=', 'project.container_id')
            ->where('project.id', '=', $projectId)
            ->first();

        $breadcrumbs = $projectRepo->getBreadcrumb($project->parent_id, [
            ['id' => $project->container_id, 'type' => 'container', 'title' => $project->container_name],
            ['id' => $projectId, 'type' => 'project', 'title' => $project->title]
        ]);

        $formName = DynamicForm::where('id', '=', $formId)->value('name');

        $formFields = array_get(
            $fieldRepo->getFieldsWithTemplate([$formId], true),
            $formId,
            []
        );

        return response()->make(array(
            'projectId' => $projectId,
            'projectTitle' => $project->title,
            'formId' => $formId,
            'formName' => $formName,
            'fields' => $formFields,
            'breadcrumbs' => $breadcrumbs,
        ));
    }

    public function formStore(Request $request, $projectId, $formId)
    {
        $project = Project::select('id')->findOrFail($projectId);

        $inputFieldValues = $request->input('formFieldValues', array());

        try
        {
            \DB::beginTransaction();

            $updateFieldIds = array();
            $insertProjectValues = array();
            foreach ($inputFieldValues as $fieldId => $formFieldValue) {
                array_push($updateFieldIds, $fieldId);

                if (is_array($formFieldValue)) {
                    $checkBoxGroupValues = array();
                    foreach ($formFieldValue as $key => $value) {
                        array_push($checkBoxGroupValues, $key);
                    }
                    $formFieldValue = json_encode($checkBoxGroupValues);
                }
                array_push($insertProjectValues, array(
                    'project_id' => $projectId,
                    'form_field_id' => $fieldId,
                    'value' => $formFieldValue
                ));
            }

            $updateAttachables = array();
            foreach ($request->input('mediaGroups', array()) as $mediaGroup) {
                foreach ($mediaGroup['items'] as $key => $item) {
                    $attachableData = array(
                        'attachment_id' => $item['attachment_id'],
                        'attachable_type' => 'App\Argo\Project',
                        'attached_form_id' => $formId,
                        'attached_at' => $item['attached_at'] ? $item['attached_at'] : date('Y-m-d H:i:s'),
                        'description' => json_encode($item['description'])
                    );

                    array_push($updateAttachables, $attachableData);
                }
            }

            \DB::table('attachables')->where('attachable_id', $projectId)
                ->where('attachable_type', '=', 'App\Argo\Project')
                ->where('attached_form_id', '=', $formId)
                ->delete();
            $project->attachments()->attach($updateAttachables);

            $project_values = ProjectValue::whereIn('form_field_id', $updateFieldIds)
                ->where('project_id', '=', $projectId)
                ->delete();

            \DB::table('project_value')->insert($insertProjectValues);

            $project->save();
            $project->touch();

            event(new ProjectValueUpdated($project->id));

            \DB::commit();

            $notificationService = new NotificationService();
            $notificationService->sendEntityUpdatedNotification(
                'App\Argo\Project',
                $projectId
            );

            return response([], 200);
        } catch (\Exception $e) {
            \DB::rollback();
            Log::error($e);
            abort(400);
        }
    }

    public function formEdit($projectId, $formId)
    {
        return view('admin.project.form_edit', [
            'projectId' => $projectId,
            'formId' => $formId
        ]);
    }

    public function formEditApi($projectId, $formId)
    {
        $valueRepo = new ProjectValueRepository();
        $attachRepo = new AttachmentRepository();
        $fieldRepo = new FormFieldRepository();
        $projectRepo = new ProjectRepository();

        $project = Project::select([
                'project.title',
                'project.parent_id',
                'container.name AS container_name',
                'container.id AS container_id',
            ])
            ->leftJoin('container', 'container.id', '=', 'project.container_id')
            ->where('project.id', '=', $projectId)
            ->first();

        $breadcrumbs = $projectRepo->getBreadcrumb($project->parent_id, [
            ['id' => $project->container_id, 'type' => 'container', 'title' => $project->container_name],
            ['id' => $projectId, 'type' => 'project', 'title' => $project->title]
        ]);

        $formName = DynamicForm::where('id', '=', $formId)->value('name');


        $formFields = array_get(
            $fieldRepo->getFieldsWithTemplate([$formId], true),
            $formId,
            []
        );

        $fieldValues = array_get(
            $valueRepo->getValuesOnFormsForProject($projectId, [$formId], true),
            $formId,
            []
        );

        $formFieldValues = array();
        foreach ($fieldValues as $key => $fieldValue) {
            $formFieldValues[$fieldValue->form_field_id] = $fieldValue->value;
            if ($fieldValue->field_template_key == 'numerical') {
                $formFieldValues[$fieldValue->form_field_id] = (int)$formFieldValues[$fieldValue->form_field_id];
            } elseif ($fieldValue->field_template_key == 'check_box_group' and $formFieldValues[$fieldValue->form_field_id]) {
                $checkBoxValues = json_decode($formFieldValues[$fieldValue->form_field_id], true);
                $formFieldValues[$fieldValue->form_field_id] = new \stdClass;

                foreach ($checkBoxValues as $checkBoxValue) {
                    $formFieldValues[$fieldValue->form_field_id]->{$checkBoxValue} = true;
                }
            }
        }

        $formMediaGroupByMonth = array_get(
            $attachRepo->getSliderOnMonths($projectId, [$formId], "App\Argo\Project"),
            $formId,
            []
        );

        return response()->make(array(
            'project_id' => $projectId,
            'project_title' => $project->title,
            'form_id' => $formId,
            'form_name' => $formName,
            'fields' => $formFields,
            'form_field_values' => $formFieldValues,
            'media_groups' => array_values($formMediaGroupByMonth),
            'breadcrumbs' => $breadcrumbs,
        ));
    }

    public function formUpdate(Request $request, $projectId, $formId)
    {
        $project = Project::select([
                'id',
                'container_id',
            ])
            ->with([
                'container'
            ])
            ->findOrFail($projectId);

        $rules = [];
        if ($request->has('project.title')) {
            if ($project->container->title_duplicatable == 1) {
                $rules['project.title'] = 'required';
            } else {
                $rules['project.title'] = 'required|unique:project,title,'.$projectId.',id,deleted_at,NULL,container_id,'.$project->container_id;
            }
        }

        $this->validate(
            $request,
            $rules,
            [
                'project.title.required' => $project->container->name.' is required.',
                'project.title.unique' => 'The '.$project->container->name.' name has been taken.',
            ]
        );

        $inputFieldValues = $request->input('formFieldValues', array());

        try
        {
            \DB::beginTransaction();

            $updateFieldIds = array();
            $insertProjectValues = array();
            foreach ($inputFieldValues as $fieldId => $formFieldValue) {
                array_push($updateFieldIds, $fieldId);
                if (is_array($formFieldValue)) {
                    $checkBoxGroupValues = array();
                    foreach ($formFieldValue as $key => $value) {
                        array_push($checkBoxGroupValues, $key);
                    }
                    $formFieldValue = json_encode($checkBoxGroupValues);
                }
                array_push($insertProjectValues, array(
                    'project_id' => $projectId,
                    'form_field_id' => $fieldId,
                    'value' => $formFieldValue
                ));
            }

            $project_values = ProjectValue::whereIn('form_field_id', $updateFieldIds)
                ->where('project_id', '=', $projectId)
                ->delete();

            \DB::table('project_value')->insert($insertProjectValues);

            if ($request->has('project.title')) {
                $project->title = $request->input('project.title');
            }

            if ($request->has('mediaGroups')) {
                $updateAttachables = array();
                foreach ($request->input('mediaGroups') as $mediaGroup) {
                    foreach ($mediaGroup['items'] as $key => $item) {
                        $attachableData = array(
                            'attachment_id' => $item['attachment_id'],
                            'attachable_type' => 'App\Argo\Project',
                            'attached_form_id' => $formId,
                            'attached_at' => $item['attached_at'] ? $item['attached_at'] : date('Y-m-d H:i:s'),
                            'description' => json_encode($item['description'])
                        );

                        array_push($updateAttachables, $attachableData);
                    }
                }

                \DB::table('attachables')->where('attachable_id', $projectId)
                    ->where('attachable_type', '=', 'App\Argo\Project')
                    ->where('attached_form_id', '=', $formId)
                    ->delete();
                $project->attachments()->attach($updateAttachables);
            }

            $project->save();
            $project->touch();

            event(new ProjectValueUpdated($project->id));

            \DB::commit();

            $notificationService = new NotificationService();
            $notificationService->sendEntityUpdatedNotification(
                'App\Argo\Project',
                $projectId
            );

            return response([], 200);
        } catch (\Exception $e) {
            \DB::rollback();
            Log::error($e);
            abort(400);
        }
    }

    public function destory($projectId)
    {
        if (!argo_is_accessible(config('argodf.delete_priority'))) {
            Log::warning("Deleting project $projectId fail, permission deny.");
            return response()->make('', 403);
        }

        try {
            $project = Project::findOrFail($projectId)->delete();
            Log::info("Project ($projectId) deleted by user (". session()->get('user_id') . ") from " . request()->ip());
        } catch (ModelNotFoundException $err) {
            Log::error("Delete project $projectId fail, id not found.");
            return response()->make('', 400);
        }

        return response()->make('', 200);
    }

    public function importExcel(Request $request)
    {
        $this->validate($request, [
            'file' => 'required'
        ]);

        try {
            $saveFileName = date('Ymd_His_').$request->file('file')->getClientOriginalName();
            \Storage::disk('imported_excel')->put($saveFileName, file_get_contents($request->file('file')));

            $protectedInfo = Excel::selectSheets(self::PROTECT_SHEET_TITLE)
                ->load(
                    $request->file('file'),
                    function ($excelReader) {
                        //
                    }
                )->first();
            $excelData = Excel::selectSheets(self::DATA_SHEET_TITLE)
                ->load(
                    $request->file('file'),
                    function ($excelReader) {
                    }
                )->get();

            $parentId = $protectedInfo['parent_id'] ? $protectedInfo['parent_id'] : null;
            $containerId = $protectedInfo['container_id'] ? $protectedInfo['container_id'] : config('argodf.default_container_id');
            $formId = $protectedInfo['form_id'] ? $protectedInfo['form_id'] : null;
            \Log::info("[Import Excel] An excel uploaded: parent id:$parentId, container id:$containerId, form id:$formId");

            //Ignore Row of Column Names.
            unset($excelData[0]);

            //Get all existing regions.
            $regionLabels = RegionLabel::pluck('name')->all();
            $regions = Region::get(['id', 'name']);

            $container = Container::find($containerId);
            if ($container && $container->title_duplicatable != 1) {
                $titles = array_pluck($excelData, 'title');
                $existsTitles = Project::whereIn('title', $titles)
                    ->where('container_id', '=', $containerId)
                    ->pluck('title')
                    ->toArray();
                $existsTitleCounts = array_count_values($existsTitles);
            } else {
                $existsTitles = [];
                $existsTitleCounts = [];
            }

            $totalRows = count($excelData);
            $createdProjectCount = 0;
            $updatedProjectCount = 0;
            $updateProjectInfos = array();
            $projectsRegions = array();
            $duplicateTitles = array();
            $updateProjectIds = array();
            $fieldValues = array();
            $createdBy = \Auth::check() ? \Auth::user()->id : null;

            foreach ($excelData as $index => $row) {
                if (!isset($formFieldIds)) {
                    $formFields = array_where($row->toArray(), function ($value, $key) {
                        return is_integer($key);
                    });
                    $formFieldIds = array();
                    foreach ($formFields as $formFieldId => $value) {
                        array_push($formFieldIds, $formFieldId);
                    }
                }

                $regionIds = array();
                if ($parentId == null) {
                    foreach ($regionLabels as $regionLabel) {
                        $row[$regionLabel] = array_first($regions->toArray(), function ($value, $key) use ($row, $regionLabel) {
                            return $value['name'] == $row[$regionLabel];
                        }, null);

                        if ($row[$regionLabel]) {
                            array_push($regionIds, $row[$regionLabel]['id']);
                        }
                    }
                }

                if ($row['project_id']) {
                    if (!$row['title'] ||
                        (array_key_exists((string)$row['title'], $existsTitleCounts) && $existsTitleCounts[$row['title']] > 1)) {
                        if ($row['title']) {
                            array_push($duplicateTitles, $row['title']);
                        }
                        unset($excelData[$index]);
                        continue;
                    }
                    $updateProjectInfo = null;
                    $updateProjectInfo = [
                        'title' => $row['title'],
                        'lat' => $row['lat'],
                        'lng' => $row['lng']
                    ];
                    $updateProjectInfo['regionIds'] = $regionIds;
                    $updateProjectInfos[$row['project_id']] = $updateProjectInfo;
                    array_push($updateProjectIds, $row['project_id']);
                    $projectId = $row['project_id'];
                } else {
                    if (!$row['title'] || in_array($row['title'], $existsTitles)) {
                        if ($row['title']) {
                            array_push($duplicateTitles, $row['title']);
                        }
                        unset($excelData[$index]);
                        continue;
                    }

                    $newProject = new Project;
                    $newProject->title = $row['title'];
                    $newProject->lat = $row['lat'];
                    $newProject->lng = $row['lng'];
                    $newProject->parent_id = $parentId;
                    $newProject->container_id = $container->id;
                    $newProject->view_level_id = config('argodf.default_perm.project.view');
                    $newProject->edit_level_id = config('argodf.default_perm.project.edit');
                    $newProject->created_by = $createdBy;
                    $newProject->save();
                    $projectId = $newProject->id;
                    $createdProjectCount += 1;
                }
                if (count($regionIds) > 0) {
                    foreach ($regionIds as $regionId) {
                        array_push($projectsRegions, [
                            'project_id' => $projectId,
                            'region_id' => $regionId
                        ]);
                    }
                }

                $projectValues = array_where($row->toArray(), function ($value, $key) {
                    return is_integer($key) and $value;
                });
                foreach ($projectValues as $fieldId => $projectValue) {
                    array_push($fieldValues, [
                        'project_id' => $projectId,
                        'form_field_id' => $fieldId,
                        'value' => $projectValue
                    ]);
                }
            }

            if ($totalRows > 0) {
                $updateProjects = Project::whereIn('id', $updateProjectIds)
                    ->get(['id', 'title', 'lat', 'lng']);

                try {
                    \DB::beginTransaction();

                    \DB::table('relation_project_belongs_region')->whereIn('project_id', $updateProjectIds)
                        ->delete();
                    \DB::table('relation_project_belongs_region')->insert($projectsRegions);

                    foreach ($updateProjects as $updateProject) {
                        if ($updateProjectInfos[$updateProject->id]['title'] != $updateProject->title) {
                            $updateProject->title = $updateProjectInfos[$updateProject->id]['title'];

                            if ($parentId == null) {
                                $updateProject->regions()->sync($regionIds);
                            }
                        }

                        $updateProject->lat = $updateProjectInfos[$updateProject->id]['lat'];
                        $updateProject->lng = $updateProjectInfos[$updateProject->id]['lng'];
                        $updateProject->save();

                        $updatedProjectCount += 1;
                    }

                    \DB::table('project_value')->whereIn('project_id', $updateProjectIds)
                        ->whereIn('form_field_id', $formFieldIds)
                        ->delete();
                    ProjectValue::insert($fieldValues);

                    \DB::commit();
                } catch (Exception $e) {
                    \DB::rollback();
                    throw new Exception($e);
                }
            }

            \Log::info("[Import Excel] An excel imported successfully:$createdProjectCount $container->name created, $updatedProjectCount $container->name updated");
            return response([
                'containerName' => $container->name,
                'createdProjectCount' => $createdProjectCount,
                'updatedProjectCount' => $updatedProjectCount,
                'duplicateTitles' => count($duplicateTitles) > 0 ? implode(", ", $duplicateTitles) : null,
            ], 200);
        } catch (Exception $e) {
            \Log::error('An error occured while importing excel.');
            \Log::error($e);
            abort(400);
        }
    }

    public function batch($parentId = null, $containerId = null)
    {
        if (!$containerId) {
            $containerId = config('argodf.default_container_id');
        }
        return view('admin.project.batch', [
            'parent_id' => $parentId,
            'container_id' => $containerId,
        ]);
    }

    public function batchApi($parentId = null, $containerId = null)
    {
        if (!$containerId) {
            $containerId = config('argodf.default_container_id');
        }

        $container = Container::where('id', '=', $containerId)->first();

        $fieldRepo = new FormFieldRepository();

        $forms = DynamicForm::get(['id', 'name']);

        $formFields = $fieldRepo->getFieldsWithTemplate(array_pluck($forms, 'id'), true);
        foreach ($forms as $form) {
            if (array_key_exists($form->id, $formFields)) {
                $form->fields = $formFields[$form->id];
            } else {
                $form->fields = [];
            }
        }

        $regionOptions = array();
        $regionLabels = array();
        if (!$parentId) {
            $regionLabels = RegionLabel::orderBy('order')
                ->get()
                ->pluck('name');

            $firstLevelRegions = Region::whereNull('parent_id')->get(['id', 'name']);
            array_push($regionOptions, $firstLevelRegions);
        }

        // Generate breadcrumbs
        if (!$parentId && !$containerId) {
            $breadcrumbs = [];
        } elseif (!$parentId) {
            $breadcrumbs = [
                ['id' => $containerId, 'type' => 'container', 'title' => $container->name],
            ];
        } else {
            $projectRepo = new ProjectRepository();
            $breadcrumbs = $projectRepo->getBreadcrumb($parentId);
        }

        return response()->json([
            'container' => $container,
            'forms' => $forms,
            'region_labels' => $regionLabels,
            'region_options' => $regionOptions,
            'breadcrumbs' => $breadcrumbs,
        ], 200, [], JSON_NUMERIC_CHECK);
    }

    public function indexExcel(Request $request, $parentProjectId = null, $containerId = null)
    {
        define('MAX_AVALIBLE_NEW_PROJECT', 100);

        //-- validate
        $inputs = [
            'projectId' => $parentProjectId,
            'containerId' => $containerId,
            'formId' => $request->input('form_id', null)
        ];
        $validator = Validator::make($inputs, [
            'projectId' => 'nullable|exists:project,id,deleted_at,NULL',
            'containerId' => 'nullable|exists:container,id',
            'formId' => 'exists:dynamic_form,id'
        ]);
        if ($validator->fails()) {
            return response(["Invalid inputs"], 400);
        }

        $isRootProject = $parentProjectId == null ? true : false;
        $containerId = $isRootProject ? config('argodf.default_container_id') : $containerId;
        $formId = $request->input('form_id', Container::find($containerId)->form_id);

        $keyRegionLabels = array();
        $titleRegionLabels = array();
        $keyFormFields = array();
        $titleFormFields = array();

        $projectedSheetData = array(
            array('parent_id', 'container_id', 'form_id'),
            array($parentProjectId, $containerId, $formId)
        );

        //-- query form
        $dyncForm = DynamicForm::select(['name'])->where('id', '=', $formId)->first();

        //-- collect region label
        if ($isRootProject) {
            $keyRegionLabels = RegionLabel::orderBy('order', 'ASC')->get()->pluck('name');
            $titleRegionLabels = array_map('studly_case', $keyRegionLabels->toArray());
        }

        //-- collect form fields
        $formFieldRepo = new FormFieldRepository();
        $formFields = $formFieldRepo->getBatchExcelFormField($formId);
        $keyFormFields = $formFields->pluck(['id']);
        $titleFormFields = $formFields->pluck(['name']);

        //-- collect projects
        $projectRepo = new ProjectRepository();
        $projects = $projectRepo->getBatchExcelProject($parentProjectId, $containerId, $formId);
        $projectIds = $projects->pluck(['id']);

        //-- collect project values
        $projectValueRepo = new ProjectValueRepository();
        $values = $projectValueRepo->getBatchExcelValues($projectIds, $keyFormFields);
        $projectValues = array();
        foreach ($values as $value) {
            $projectValues["$value->project_id-$value->form_field_id"] = $value->value;
        }

        //-- merge sheet data array
        $row1 = array_collapse(array(array('modified', 'project_id', 'title', 'lat', 'lng'), $keyRegionLabels, $keyFormFields));
        $row2 = array_collapse(array(array('', 'Id', 'Name', 'Lat', 'Lng'), $titleRegionLabels, $titleFormFields));

        //-- prepare rows
        $rows = array($row1, $row2);
        foreach ($projects as $project) {
            $row = array('', $project->id, $project->title, $project->lat, $project->lng);
            if ($isRootProject) {
                $pluckedRegion = array_pluck($project->regions, 'name', 'label_name');
                foreach ($keyRegionLabels as $label) {
                    array_push($row, array_get($pluckedRegion, $label, ''));
                }
            }
            foreach ($keyFormFields as $formFieldId) {
                array_push($row, array_get($projectValues, "$project->id-$formFieldId", ''));
            }
            array_push($rows, $row);
        }

        //-- calculate the range of editable cells, and other cells will be protected
        //-- MAX_AVALIBLE_NEW_PROJECT is the number avalible for new
        $columnString = \PHPExcel_Cell::stringFromColumnIndex(count($row1) - 1);
        $cellsEditableRange = 'C3:'.$columnString.(count($projects) + MAX_AVALIBLE_NEW_PROJECT);

        try {
            //-- export excel
            Excel::create($dyncForm->name, function ($excel) use ($rows, $projectedSheetData, $cellsEditableRange) {
                //-- main sheet
                $excel->sheet(self::DATA_SHEET_TITLE, function ($sheet) use ($rows, $cellsEditableRange) {
                    $sheet->rows($rows);

                    //-- style
                    $sheet->getRowDimension(1)->setVisible(false);
                    $sheet->getColumnDimension('A')->setVisible(false);
                    $sheet->getColumnDimension('B')->setVisible(false);

                    //-- protect all, mark editable cell editable
                    $sheet->protect(self::SHEET_PWD);
                    $sheet->getStyle($cellsEditableRange)
                        ->getProtection()
                        ->setLocked(\PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);

                    //-- Mark all editable cell as text format to handle 0 leading values
                    $sheet->setColumnFormat([
                        $cellsEditableRange => '@'
                    ]);
                });
                //-- protected sheet
                $excel->sheet(self::PROTECT_SHEET_TITLE, function ($sheet) use ($projectedSheetData) {
                    $sheet->fromArray($projectedSheetData, null, 'A1', false, false);

                    $sheet->protect(self::SHEET_PWD);
                });
            })->download('xls');
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(["Export excel error"], 400);
        }
    }
}
