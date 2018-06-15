<?php

namespace App\Argo;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, SoftDeletes;

    protected $table = 'user';

    protected $hidden = array('password', 'remember_token');

    protected $casts = [
        'notification_enabled' => 'boolean',
    ];

    public function permissionLevel()
    {
        return $this->belongsTo('App\Argo\PermissionLevel');
    }

    public function projects()
    {
        return $this->belongsToMany('App\Argo\Project', 'relation_user_own_project', 'user_id', 'project_id');
    }

    public function permission_level()
    {
        return $this->belongsTo('App\Argo\PermissionLevel');
    }
}
