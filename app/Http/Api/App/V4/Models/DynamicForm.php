<?php

namespace App\Http\Api\App\V4\Model;

use App\Argo\DynamicForm as OriginModel;

class DynamicForm extends OriginModel
{
    protected $casts = [];
    // The is_photo_reuqired should not in the casts to prevent App v4.0.3 crash
}
