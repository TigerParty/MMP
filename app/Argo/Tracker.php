<?php

namespace App\Argo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tracker extends Model
{
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'tracker';

    protected $casts = [
        'path' => 'json',
        'meta' => 'json'
    ];

    public function createdBy()
    {
        return $this->belongsTo('App\Argo\User');
    }

    public function attaches()
    {
        return $this->morphToMany('App\Argo\Attachment', 'attachable');
    }
}
