<?php

namespace App\Argo;

use Illuminate\Database\Eloquent\Model;

class DynamicConfig extends Model
{
    public $timestamps = false;

    protected $table = 'dynamic_config';

    protected $primaryKey = 'key';

    public $incrementing = false;
}
