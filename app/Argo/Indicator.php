<?php

namespace App\Argo;

use Illuminate\Database\Eloquent\Model;

class Indicator extends Model
{
    public $timestamps = false;

    protected $table = 'indicator';

    protected $casts = [
        'title' => 'json',
        'options' => 'json',
        'yaxis' => 'json',
        'data_fields' => 'json',
    ];

    public function indicate()
    {
        return $this->morphTo();
    }
}
