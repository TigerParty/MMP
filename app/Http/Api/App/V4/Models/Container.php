<?php

namespace App\Http\Api\App\V4\Model;

use App\Argo\Container as OriginModel;

class Container extends OriginModel
{
    protected $casts = [];
    // The reportable should not in the casts to prevent App v4.0.3 crash
}
