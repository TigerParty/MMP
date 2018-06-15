<?php

namespace App\Http\Api\App\V4\Model;

use App\Argo\FormField as OriginModel;

class FormField extends OriginModel
{
    protected $casts = [];
    // The is_reuqired should not in the casts to prevent App v4.0.3 crash
}
