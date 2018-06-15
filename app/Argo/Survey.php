<?php

namespace App\Argo;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    public $timestamps = true;

    protected $table = 'survey';

    protected $casts = [
        'payload' => 'json'
    ];
}
