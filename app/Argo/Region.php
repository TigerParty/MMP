<?php

namespace App\Argo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
    use SoftDeletes;
    public $timestamps = false;

    protected $table = 'region';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
    protected $hidden = ['deleted_at'];

    public function projects()
    {
        return $this->belongsToMany(
            'App\Argo\Project',
            'relation_project_belongs_region',
            'region_id',
            'project_id'
        );
    }

    public function indicators()
    {
        return $this->morphMany('App\Argo\Indicator', 'indicate');
    }

    public function scopeSubregions($query, $parentId)
    {
        if ($parentId === 0) {
            return $query->whereNull('parent_id')
                ->orderBy('order');
        }
        return $query->where('parent_id', '=', $parentId)
            ->orderBy('order');
    }
}
