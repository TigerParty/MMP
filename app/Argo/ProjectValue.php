<?php

namespace App\Argo;

use Illuminate\Database\Eloquent\Model;

class ProjectValue extends Model
{
    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'project_value';

    protected $primaryKey = ['project_id', 'form_field_id'];
}
