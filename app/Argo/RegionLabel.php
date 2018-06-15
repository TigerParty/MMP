<?php

namespace App\Argo;

use Illuminate\Database\Eloquent\Model;

class RegionLabel extends Model
{
    public $timestamps = false;

    protected $table = 'region_label';

    protected $primaryKey = 'name';

    public $incrementing = false;
}
