<?php

namespace App\Argo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $table = 'report';
    protected $primaryKey = 'id';
    protected $hidden = ['view_level_id', 'edit_level_id'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'lat' => 'double',
        'lng' => 'double',
        'region_ids' => 'array',
    ];

    protected $fillable = [
        'form_id', 'project_id', 'description', 'lat', 'lng',
        'view_level_id', 'edit_level_id', 'created_by', 'region_ids'
    ];

    public function form_fields()
    {
        return $this->belongsToMany('App\Argo\FormField', 'report_value', 'report_id', 'form_field_id')->withPivot('value');
    }

    public function gps_tracker_field()
    {
        return $this->belongsToMany('App\Argo\FormField', 'report_value', 'report_id', 'form_field_id')->where('field_template_id', '=', 8)->withPivot('value');
    }

    public function dynamic_form()
    {
        return $this->belongsTo('App\Argo\DynamicForm', 'form_id', 'id');
    }

    public function project()
    {
        return $this->belongsTo('App\Argo\Project', 'project_id', 'id');
    }

    public function files()
    {
        return $this->morphToMany('App\Argo\Attachment', 'attachable')
            ->withPivot('attached_at');
    }

    public function attachments()
    {
        return $this->morphToMany('App\Argo\Attachment', 'attachable')
            ->withPivot('description')
            ->where('type', 'NOT LIKE', 'image/%');
    }

    public function images()
    {
        return $this->morphToMany('App\Argo\Attachment', 'attachable')
            ->withPivot('description')
            ->where('type', 'LIKE', 'image/%')
            ->orWhere('type', 'LIKE', 'video/%');
    }

    public function view_level()
    {
        return $this->belongsTo('App\Argo\PermissionLevel', 'view_level_id', 'id');
    }

    public function edit_level()
    {
        return $this->belongsTo('App\Argo\PermissionLevel', 'edit_level_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo('App\Argo\User', 'created_by', 'id');
    }

    public function updater()
    {
        return $this->belongsTo('App\Argo\User', 'updated_by', 'id');
    }

    public function notifications()
    {
        return $this->morphMany('App\Argo\Notification', 'notify');
    }
}
