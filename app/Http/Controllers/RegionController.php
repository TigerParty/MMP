<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Argo\Region;
use App\Argo\Project;
use App\Argo\RegionLabel;
use App\Repositories\RegionRepository;

class RegionController extends Controller
{
    public function index()
    {
        $repo = new RegionRepository();
        return view('region.index', ['rootRegions'=>$repo->getRootRegions()]);
    }

    public function indexRootApi()
    {
        $regionLabel = RegionLabel::select('name')
            ->orderBy('order')
            ->first();

        if ($regionLabel) {
            $rootLabel = $regionLabel->name;
        }

        $regions = Region::select(['id', 'name', 'label_name'])
            ->whereNull('parent_id')
            ->where('label_name', '=', $rootLabel)
            ->orderBy('order')
            ->get();

        return response()->json($regions, 200, [], JSON_NUMERIC_CHECK);
    }

    public function show($id)
    {
        return view('region.show', ['regionId'=> $id]);
    }

    public function showApi($id)
    {
        $region = Region::select(['id', 'name', 'label_name'])
            ->find($id);

        $subRegions = Region::select(['id', 'name', 'label_name'])
            ->where('parent_id', '=', $id)
            ->orderBy('order')
            ->get();

        $subregionFirst = array_first($subRegions, function ($subRegion, $key){
            return $subRegion;
        });
        $subregionLabel = $subregionFirst? $subregionFirst->label_name:'';

        foreach ($subRegions as $subRegion) {
            $subRegion->path = url("/region/$subRegion->id");
        }

        $region->subregion_label = $subregionLabel;
        $region->subregions = $subRegions;

        return response()->json($region, 200, [], JSON_NUMERIC_CHECK);
    }

    public function projectIndexApi($regionId) {
      $projects = Project::whereHas('regions', function($subquery) use ($regionId){
          $subquery->where('id', '=', $regionId);
      })->select(['id', 'title', 'cover_image_id', 'description'])
      ->get();

      foreach ($projects as $project) {
          $project->cover_image_path = argo_image_path($project->cover_image_id);
          $project->path = url("/project/$project->id");
          unset($project->cover_image_id);
      }

      return response()->json($projects, 200, [], JSON_NUMERIC_CHECK);
    }
}
