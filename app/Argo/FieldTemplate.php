<?php

namespace App\Argo;

use Illuminate\Database\Eloquent\Model;

class FieldTemplate extends Model
{
    public $timestamps = false;
    protected $table = 'field_template';
    protected $primaryKey = 'id';

    public function form_fields()
    {
        return $this->hasMany('App\Argo\FormField', 'field_template_id');
    }
}
