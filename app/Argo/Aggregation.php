<?php

namespace App\Argo;

use Illuminate\Database\Eloquent\Model;

class Aggregation extends Model
{
    public $timestamps = false;

    protected $table = 'aggregation';

    protected $casts = [
        "filters" => "array"
    ];
}
