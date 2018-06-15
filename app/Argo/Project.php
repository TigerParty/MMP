<?php

namespace App\Argo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $table = 'project';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $hidden = ['view_level_id', 'edit_level_id'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'group_id' => 'integer',
        'lat' => 'double',
        'lng' => 'double',
    ];

    protected $fillable = [
        'group_id', 'title', 'description', 'view_level_id', 'edit_level_id',
        'default_form_id', 'project_status_id', 'default_img_id', 'cover_image_id',
        'lat', 'lng', 'parent_id', 'container_id', 'uid'
    ];

    /*
    |--------------------------------------------------------------------------
    | Force datetime serize format to output as ISO 8601
    |--------------------------------------------------------------------------
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('c');
    }

    /*
    |--------------------------------------------------------------------------
    | One to Many relations
    |--------------------------------------------------------------------------
    */
    public function values()
    {
        return $this->hasMany('App\Argo\ProjectValue', 'project_id', 'id');
    }

    public function reports()
    {
        return $this->hasMany('App\Argo\Report', 'project_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Many to One relations
    |--------------------------------------------------------------------------
    */
    public function container()
    {
        return $this->belongsTo('App\Argo\Container', 'container_id', 'id');
    }

    public function creater()
    {
        return $this->belongsTo('App\Argo\User', 'created_by', 'id');
    }

    public function view_level()
    {
        return $this->belongsTo('App\Argo\PermissionLevel', 'view_level_id', 'id');
    }

    public function edit_level()
    {
        return $this->belongsTo('App\Argo\PermissionLevel', 'edit_level_id', 'id');
    }

    public function dynamic_form()
    {
        return $this->belongsTo('App\Argo\DynamicForm', 'default_form_id', 'id');
    }

    public function status()
    {
        return $this->belongsTo('App\Argo\ProjectStatus', 'project_status_id', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | Many to Many relations
    |--------------------------------------------------------------------------
    */
    public function regions()
    {
        return $this->belongsToMany('App\Argo\Region', 'relation_project_belongs_region', 'project_id', 'region_id');
    }

    public function charts()
    {
        return $this->belongsToMany('App\Argo\Chart', 'relation_project_has_chart', 'project_id', 'chart_id');
    }

    public function notifications()
    {
        return $this->morphMany('App\Argo\Notification', 'notify');
    }

    public function notification_smses()
    {
        return $this->morphMany('App\Argo\NotificationSMS', 'notify');
    }

    public function attaches()
    {
        return $this->morphToMany('App\Argo\Attachment', 'attachable');
    }

    public function owners()
    {
        return $this->belongsToMany('App\Argo\User', 'relation_user_own_project', 'project_id', 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Refactor line
    |--------------------------------------------------------------------------
    | All functions under this line are not yet reviewed and might be removed in future
    */
    public function subprojects()
    {
        return $this->hasMany('App\Argo\Project', 'parent_id', 'id');
    }

    public function gps_tracker_field()
    {
        return $this->belongsToMany('App\Argo\FormField', 'project_value', 'project_id', 'form_field_id')
            ->where('field_template_id', '=', 8)
            ->withPivot('value');
    }

    public function iri_tracker()
    {
        return $this->belongsToMany('App\Argo\FormField', 'project_value', 'project_id', 'form_field_id')
            ->where('field_template_id', '=', 10)->withPivot('value');
    }

    public function last_report()
    {
        return $this->hasOne('App\Argo\Report', 'project_id')
            ->orderBy('updated_at', 'DESC');
    }

    public function files()
    {
        return $this->morphToMany('App\Argo\Attachment', 'attachable')
            ->withPivot('attached_at');
    }

    //-- DEPRECATED! Please don't use this function anymore, will refactor in future
    public function attachments()
    {
        return $this->morphToMany('App\Argo\Attachment', 'attachable')
            ->where('type', 'NOT LIKE', 'image/%');
    }
}
