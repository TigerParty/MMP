<?php
namespace App\Repositories;

use DB;
use App\Argo\FormField;
use App\Argo\ProjectValue;

class ProjectValueRepository {
    public function getValuesOnFormsForProject($projectId, $formIds, $isEditing = false)
    {
        $orm = FormField::select([
            DB::raw(app('db')->getPdo()->quote($projectId) . ' AS `project_id`'),
            'form_field.id AS form_field_id',
            'field_template.key AS field_template_key',
            'form_field.form_id',
            'form_field.name',
            'project_value.value',
            'form_field.show_if'
        ])
            ->leftJoin('field_template', 'field_template.id', '=', 'form_field.field_template_id')
            ->leftJoin('project_value', function($join) use ($projectId) {
                $join->on('form_field.id', '=', 'project_value.form_field_id')
                    ->on('project_value.project_id', '=', DB::raw(app('db')->getPdo()->quote($projectId)) );
            })
            ->whereIn('form_field.form_id', $formIds)
            ->whereNull('form_field.deleted_at')
            ->orderBy('form_field.order');

        if ($isEditing) {
            $orm->where('form_field.edit_level_id', ">=", argo_current_permission());
        } else {
            $orm->where('form_field.view_level_id', ">=", argo_current_permission());
        }

        $projectValues = $orm->get();

        $valuesOnFormId = [];
        foreach($projectValues as $projectValue) {
            if(!array_key_exists($projectValue->form_id, $valuesOnFormId)) {
                $valuesOnFormId[$projectValue->form_id] = [];
            }

            try{
                if($projectValue->show_if) {
                    // Workaround before refactor show_if trigger
                    // Origin format in DB:     {"10":["Day"]}
                    // New format for refactor: {"10":"Day"}
                    $showIfObj= json_decode($projectValue->show_if);
                    $arrangedShowIf = [];
                    foreach($showIfObj as $triggerId => $triggerValue) {
                        $arrangedShowIf[$triggerId] = $triggerValue[0];
                    }
                    $projectValue->show_if = $arrangedShowIf;
                }
            } catch (\Exception $err) {
                \Log::warning("Field $projectValue->form_field_id has illegal show_if value");
                $projectValue->show_if = null;
            }

            array_push($valuesOnFormId[$projectValue->form_id], $projectValue);
        }

        return $valuesOnFormId;
    }

    public function getFormsFromExistValues($projectId, $excludeFormIds = [])
    {
        $result = DB::table('project_value')
            ->leftJoin('form_field', 'form_field.id', '=', 'project_value.form_field_id')
            ->leftJoin('dynamic_form', 'dynamic_form.id', '=', 'form_field.form_id')
            ->where('project_value.project_id', '=', $projectId)
            ->whereNull('dynamic_form.deleted_at')
            ->whereNotIn('dynamic_form.id', $excludeFormIds)
            ->groupBy('dynamic_form.id')
            ->orderBy('dynamic_form.name')
            ->get([
                'dynamic_form.id',
                'dynamic_form.name'
            ]);
        return $result;
    }

    public function getProjectSpeedTrackers($projectId)
    {
        $rows = DB::table('project_value')
            ->leftJoin('form_field', 'form_field.id', '=', 'project_value.form_field_id')
            ->leftJoin('field_template', 'field_template.id', '=', 'form_field.field_template_id')
            ->where('project_value.project_id', '=', $projectId)
            ->where('field_template.key', '=', 'gps_tracker')
            ->get(['project_value.value']);

        $result = [];
        foreach($rows as $row)
        {
            array_push($result, json_decode($row->value));
        }
        return $result;
    }

    public function getBatchExcelValues($projectIds = [], $formFieldIds = [])
    {
        return ProjectValue::select([
            'project_id',
            'form_field_id',
            'value'
        ])
            ->whereIn('project_id', $projectIds)
            ->whereIn('form_field_id', $formFieldIds)
            ->get();
    }
}
