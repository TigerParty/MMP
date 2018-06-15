<?php

namespace App\Repositories;

use App\Argo\DynamicForm;

class DynamicFormRepository
{
    public function getDynamicFormField($form_id)
    {
        $dynamic_form = DynamicForm::with(array(
                'fields' => function ($query) {
                    $query->select([
                        'form_field.id AS id',
                        'form_field.name AS name',
                        'default_value AS default',
                        'options AS options',
                        'order AS order',
                        'form_id',
                        'ft.html AS template_name'
                    ])
                        ->leftJoin('field_template AS ft', 'form_field.field_template_id', '=', 'ft.id')
                        ->orderBy('order', 'ASC');
                })
        )
            ->where('id', '=', $form_id)
            ->first();

        $dynamic_form->use_default = false;

        return $dynamic_form;
    }

    public function getDynamicFormFieldByPerm($form_id)
    {
        $dynamic_form = DynamicForm::with(array(
                'fields' => function ($query) {
                    $query->select([
                        'form_field.id AS id',
                        'form_field.name AS name',
                        'default_value AS default',
                        'options AS options',
                        'order AS order',
                        'form_id',
                        'ft.html AS template_name'
                    ])
                        ->leftJoin('field_template AS ft', 'form_field.field_template_id', '=', 'ft.id')
                        ->leftjoin('permission_level as vl', 'form_field.edit_level_id', '=', 'vl.id')
                        ->where('vl.priority', '>=', argo_current_permission())
                        ->orderBy('order', 'ASC');
                })
        )
            ->where('id', '=', $form_id)
            ->first();

        return $dynamic_form;
    }

    public function getFieldIds($form_id)
    {

        $field_ids = array();
        $fields = DynamicForm::find($form_id)->fields()->get();

        foreach ($fields as $field) {
            $field_ids[$field->id] = '';
        }
        return $field_ids;
    }

    public function getDynamicFormsForAdmin()
    {
        $dynamic_forms = DynamicForm::with(array(
                'fields' => function ($query) {
                    $query->with(array(
                        'template',
                        'edit_level' => function ($query) {
                            $query->select(['id', 'name']);
                        },
                        'view_level' => function ($query) {
                            $query->select(['id', 'name']);
                        }
                    ))
                        ->select([
                            'form_field.id AS id',
                            'form_field.name AS name',
                            'default_value AS default',
                            'options AS options',
                            'order AS order',
                            'form_id',
                            'field_template_id',
                            'view_level_id',
                            'edit_level_id',
                            'show_if',
                            'is_required'
                        ])
                        ->orderBy('order', 'ASC');
                })
        );
        return $dynamic_forms;
    }

    public function getDynamicForms($access_priority)
    {
        $dynamic_forms = DynamicForm::with(array(
                'fields' => function ($query) use ($access_priority) {
                    $query->select([
                        'form_field.id AS id',
                        'form_field.name AS name',
                        'default_value AS default',
                        'options AS options',
                        'order AS order',
                        'form_id',
                        'ft.html AS template_name',
                        'show_if',
                        'is_required'
                    ])
                        ->leftJoin('field_template AS ft', 'form_field.field_template_id', '=', 'ft.id')
                        ->orderBy('order', 'ASC');
                })
        );
        return $dynamic_forms;
    }
}
