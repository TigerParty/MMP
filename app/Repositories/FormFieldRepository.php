<?php
namespace App\Repositories;

use DB;
use App\Argo\FormField;

class FormFieldRepository {
    public function getFilterableFields($containerIds)
    {
        $fields = DB::table('form_field')
            ->leftJoin('field_template', 'field_template.id', '=', 'form_field.field_template_id')
            ->whereIn('form_field.id', function($query) use ($containerIds){
                $query->select('form_field_id')
                    ->from('relation_field_filter_container')
                    ->whereIn('container_id', $containerIds);
            })
            ->get([
                'form_field.id',
                'form_field.name',
                'form_field.form_id',
                'form_field.options',
                'field_template.filter_key AS filter_key',
                'field_template.key AS key',
            ]);

        foreach($fields as $field)
        {
            $field->options = json_decode($field->options);
        }

        return $fields;
    }

    public function getFieldsWithTemplate($formIds, $isEditing = false)
    {
        if (!$formIds) {
            return [];
        }

        $orm = DB::table('form_field')
            ->leftJoin('field_template', 'field_template.id', '=', 'form_field.field_template_id')
            ->whereNull('deleted_at')
            ->whereIn('form_id', $formIds)
            ->orderBy('order');

        if ($isEditing) {
            $orm->where('form_field.edit_level_id', '>=', argo_current_permission());
        } else {
            $orm->where('form_field.view_level_id', '>=', argo_current_permission());
        }

        $fields = $orm->get([
            'form_field.id',
            'form_field.name',
            'form_field.form_id',
            'form_field.field_template_id',
            'form_field.options',
            'form_field.show_if',
            'form_field.formula',
            'field_template.key AS key',
            'field_template.filter_key AS filter_key',
        ]);

        $fieldsOnForm = [];
        foreach($fields as $field)
        {
            if (!array_key_exists($field->form_id, $fieldsOnForm)) {
                $fieldsOnForm[$field->form_id] = [];
            }

            if ($field->options) {
                $field->options = json_decode($field->options);
            }

            if ($field->show_if) {
                $field->show_if = json_decode($field->show_if);
            }
            array_push($fieldsOnForm[$field->form_id], $field);
        }

        return $fieldsOnForm;
    }

    public function getBatchExcelFormField($formId)
    {
        return FormField::select([
            'form_field.id',
            'form_field.name',
        ])
            ->leftJoin('field_template', 'form_field.field_template_id', '=', 'field_template.id')
            ->where('form_field.form_id', '=', $formId)
            ->whereNotIn('field_template.key', ['tracker', 'check_box_group', 'iri_tracker'])
            ->where('form_field.edit_level_id', '>=', argo_current_permission())
            ->whereNull('form_field.deleted_at')
            ->orderBy('form_field.order')
            ->get();
    }
}
