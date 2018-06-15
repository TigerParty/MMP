<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Argo\Container;
use App\Argo\RegionLabel;
use App\Repositories\FormFieldRepository;

class ContainerController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Container Show API
    |--------------------------------------------------------------------------
    | Parameters: excludes[], if 'filters' in this array
    |             API will not execute filters' query
    |
    */
    public function showApi($containerId)
    {
        $container = Container::find($containerId);

        if (!$container) {
            return response()->json([], 404);
        }

        $fieldRepo = new FormFieldRepository();
        $excludes = is_array(request()->input('excludes')) ? request()->input('excludes') : [];

        $responseArray = [
            'id' => $containerId,
            'name' => $container->name,
            'parent_id' => $container->parent_id,
            'form_id' => $container->form_id,
        ];

        if (!in_array('filters', $excludes)) {
            $filters = [];

            // Only give regions filter to root containers
            if ($container->parent_id == null) {
                $regionLabels = RegionLabel::orderBy('order')
                    ->get()
                    ->pluck('name');
                $filters['region_labels'] = $regionLabels;
            }

            // Arrange fields fileter into better accessable format
            $filters['fields'] = $fieldRepo->getFilterableFields([$containerId]);

            $responseArray['filters'] = $filters;
        }

        return response()->json($responseArray, 200, [], JSON_NUMERIC_CHECK);
    }
}
