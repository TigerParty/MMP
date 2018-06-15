<?php

namespace App\Argo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormField extends Model
{
    use SoftDeletes;

    protected $table = 'form_field';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $hidden = ['deleted_at'];

    protected $casts = [
        'options' => 'array',
        'order' => 'integer',
        'show_if' => 'array',
        'is_required' => 'boolean',
        'formula' => 'array',
    ];

    public function form()
    {
        return $this->belongsTo('App\Argo\Form', 'form_id', 'id');
    }

    public function filterContainers()
    {
        return $this->belongsToMany('App\Argo\Container', 'relation_field_filter_container', 'form_field_id', 'container_id');
    }

    public function project_value()
    {
        return $this->hasOne('App\Argo\ProjectValue', 'form_field_id', 'id');
    }

    public function report_value()
    {
        return $this->hasOne('App\Argo\ReportValue', 'form_field_id', 'id');
    }

    public function template()
    {
        return $this->belongsTo('App\Argo\FieldTemplate', 'field_template_id', 'id');
    }

    public function view_level()
    {
        return $this->belongsTo('App\Argo\PermissionLevel', 'view_level_id', 'id');
    }

    public function edit_level()
    {
        return $this->belongsTo('App\Argo\PermissionLevel', 'edit_level_id', 'id');
    }

    public function scopeFormFieldsName($query, $form_id, $selected_fieldIDs)
    {
        $fields = $this->where('form_id','=',$form_id)
            ->select('name');

        if(!empty($selected_fieldIDs)){
            $fields->whereIn('id',$selected_fieldIDs);
        }

        $fields->orderBy('order','ASC')
            ->orderBy('id','ASC');

        return $fields;
    }
}
