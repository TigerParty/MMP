<?php

namespace App\Argo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectStatus extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    protected $table = 'project_status';

    protected $fillable = ['name', 'default'];

    protected $casts = [
        'default' => 'boolean',
    ];

    public function projects()
    {
        return $this->hasMany('App\Argo\Project');
    }

    public function scopeDefaultProjectStatus($query)
    {
        return $query->where('default', '=', 1)->first();
    }
}
