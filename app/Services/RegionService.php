<?php

namespace App\Services;

use App\Argo\RegionLabel;
use Log;

class RegionService
{
    public function arrangeRegionGroupByLabel($regions, $regionLabels = null)
    {
        if (!$regionLabels) {
            Log::warning("RegionService@arrangeRegionGroupByLabel not get cached regionLabels, this might effect performance.");
            $regionLabels = RegionLabel::orderBy('order')
                ->get();
        }

        $arrangedRegions = [];
        foreach ($regionLabels as $regionLabel) {
            $region = array_first($regions, function ($value, $key) use ($regionLabel) {
                return $value->label_name == $regionLabel->name;
            });

            if ($region) {
                array_push($arrangedRegions, [
                    'id' => $region->id,
                    'name' => $region->name,
                    'label' => $regionLabel->name,
                    'label_order' => $regionLabel->order
                ]);
            } else {
                array_push($arrangedRegions, [
                    'id' => null,
                    'name' => null,
                    'label' => $regionLabel->name,
                    'label_order' => $regionLabel->order
                ]);
            }
        }

        return $arrangedRegions;
    }

    public function arrangeRegionKeyByLabel($regions, $hidePivot = true)
    {
        $arrangedRegions = [];
        foreach ($regions as $region) {
            if ($hidePivot) {
                unset($region->pivot); // Hide pivot for response
            }
            $arrangedRegions[$region->label_name] = $region;
        }
        return $arrangedRegions;
    }
}
