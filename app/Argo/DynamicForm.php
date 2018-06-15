<?php

namespace App\Argo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DynamicForm extends Model
{
    use SoftDeletes;

    public $timestamps = false;
    protected $table = 'dynamic_form';
    protected $primaryKey = 'id';
    protected $hidden = ['deleted_at'];

    protected $dates = [
        'deleted_at'
    ];

    protected $casts = [
        'is_photo_required' => 'boolean',
    ];

    public function fields()
    {
        return $this->hasMany('App\Argo\FormField', 'form_id');
    }

    public function numerical_fields()
    {
        return $this->hasMany('App\Argo\FormField', 'form_id')->where('field_template_id', '=', 6);
    }

    public function projects()
    {
        return $this->hasMany('App\Argo\Project', 'default_form_id');
    }

    public function reports()
    {
        return $this->hasMany('App\Argo\Report', 'form_id');
    }

    public function last_report()
    {
        return $this->hasOne('App\Argo\Report', 'form_id')->orderBy('updated_at', 'DESC')->orderBy('id', 'DESC');
    }

    public function scopeGetFieldIds($query, $form_id)
    {
        $field_ids = array();
        $fields = $this->find($form_id)->fields()->get();
        foreach($fields as $field)
        {
            $field_ids[$field->id] = '';
        }
        return $field_ids;
    }

    public function scopeGetDynamicFormsForAdmin($query)
    {
        $dynamic_forms = $this->with(array(
                'fields' => function($query) {
                    $query->with(array(
                        'template',
                        'edit_level' => function($query) {
                            $query->select(['id', 'name']);
                        },
                        'view_level' => function($query) {
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
                        ->orderBy('order','ASC');
                })
        );
        return $dynamic_forms;
    }

    public function scopeGetDynamicForms($query, $access_priority)
    {
        $dynamic_forms = $this->with(array(
                'fields' => function($query) use ($access_priority) {
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
                        ->orderBy('order','ASC');
                })
        );
        return $dynamic_forms;
    }
}
