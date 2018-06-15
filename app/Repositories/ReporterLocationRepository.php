<?php

namespace App\Repositories;

use App\Argo\ReporterLocation;

class ReporterLocationRepository
{
    public static function getReporterLastLocation()
    {
        return ReporterLocation::select(DB::raw(
            'reporter_location.device_id,' .
            'max(reporter_location.created_at) AS created_at,' .
            'sub.lat, sub.lng, sub.created_by, sub.created_at')
        )
            ->GroupBy('reporter_location.device_id')
            ->leftJoin('reporter_location AS sub', function ($query) {
                $query->on('sub.device_id', '=', 'reporter_location.device_id')
                    ->on('sub.created_at', '=', 'reporter_location.created_at');
            });
    }
}
