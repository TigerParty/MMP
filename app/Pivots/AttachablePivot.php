<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\MorphPivot;

class AttachablePivot extends MorphPivot
{
    protected $hidden = ['attachable_type', 'attachment_id', 'attachable_id'];

    protected $casts = [
        'description' => 'array'
    ];
}
