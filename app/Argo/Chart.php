<?php

namespace App\Argo;

use Illuminate\Database\Eloquent\Model;

class Chart extends Model
{
    protected $table = 'chart';

    public function projects()
    {
    	return $this->belongsToMany('App\Argo\Project', 'relation_project_has_chart', 'chart_id', 'project_id');
    }
}
