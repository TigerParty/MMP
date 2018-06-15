<?php

namespace App\Repositories;

use App\Argo\PermissionLevel;

class PermissionLevelRepository
{
    public function getAvailableLevels($access_priority)
    {
        return PermissionLevel::AvailableLevels($access_priority)
            ->select(array('id', 'name'))
            ->orderBy('priority')
            ->get();
    }
}
