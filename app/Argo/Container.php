<?php

namespace App\Argo;

use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    public $timestamps = false;
    protected $table = 'container';
    protected $primaryKey = 'id';
    protected $casts = [
        'reportable' => 'boolean',
        'title_duplicatable' => 'boolean',
        'uid_rule' => 'array',
        'card_rule' => 'array',
    ];

    public function subcontainers()
    {
        return $this->hasMany('App\Argo\Container', 'parent_id', 'id');
    }

    public function form()
    {
        return $this->belongsTo('App\Argo\DynamicForm', 'form_id', 'id');
    }

    public function aggregations()
    {
        return $this->hasMany('App\Argo\Aggregation', 'container_id', 'id');
    }

    public function indicators()
    {
        return $this->morphMany('App\Argo\Indicator', 'indicate');
    }
}
