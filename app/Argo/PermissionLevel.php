<?php

namespace App\Argo;

use Illuminate\Database\Eloquent\Model;

class PermissionLevel extends Model
{
    public $timestamps = false;

    protected $table = 'permission_level';

    protected $primaryKey = 'id';

    public function scopeGetAvailableLevels($query, $access_priority) {
        return $this->select(['id', 'name'])
            ->where('priority', '>=', $access_priority)
            ->orderBy('priority')
            ->get();
    }
}
