<?php

namespace App\Repositories;

use App\Argo\Region;
use App\Argo\RegionLabel;
use Log;

class RegionRepository
{
    /*
        |--------------------------------------------------------------------------
        | Declare maximum deep hard limit
        |--------------------------------------------------------------------------
        | To prevent infinite level recursive
        | Maximum_level declare the hard limit of recursive deepth
        */
    private $level_hard_limit = 5;

    /*
    |--------------------------------------------------------------------------
    | Get Region object in tree structured
    |--------------------------------------------------------------------------
    | Parm: $maximum_level as soft limit of maximum deep go.
    | Return: a tree structed array
    */
    public function getTree($level_soft_limit = null)
    {
        $maximum_level = ($level_soft_limit) ?
            min($level_soft_limit, $this->level_hard_limit) :
            $this->level_hard_limit;

        $regions = Region::orderBy('order')->get();

        return $this->getSubTree([], null, $regions, 1, $maximum_level);
    }

    /*
    |--------------------------------------------------------------------------
    | Get sub tree of region entity with recursive call
    |--------------------------------------------------------------------------
    | Level is a integer, start from 0
    | Return a tree structed array
    */
    public function getSubTree($tree, $parent_id, $regions, $current_level, $maximum_level)
    {
        if ($current_level > $maximum_level) {
            Log::warning("RegionRepository@getSubTree over maximum level limit: parent_id $parent_id, current_level $current_level");
            return;
        }

        $current_regions = array_where($regions, function ($value, $key) use ($parent_id) {
            return $value->parent_id == $parent_id;
        });

        foreach ($current_regions as $region) {
            $region->children = $this->getSubTree([], $region->id, $regions, $current_level + 1, $maximum_level);
            array_push($tree, $region);
        }

        return $tree;
    }

    /*
    |--------------------------------------------------------------------------
    | Get root regions
    |--------------------------------------------------------------------------
    */
    public function getRootRegions($rootLabel = null)
    {
        if (!$rootLabel) {
            $regionLabel = RegionLabel::select('name')
                ->orderBy('order')
                ->first();

            if ($regionLabel) {
                $rootLabel = $regionLabel->name;
            }
        }

        return Region::select(['id', 'name', 'label_name', 'map_path', 'map_title_x', 'map_title_y'])
            ->whereNull('parent_id')
            ->where('label_name', '=', $rootLabel)
            ->orderBy('order')
            ->get();
    }
}
