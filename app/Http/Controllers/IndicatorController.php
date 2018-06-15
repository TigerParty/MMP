<?php

namespace App\Http\Controllers;

use App\Services\IndicatorService;
use App\Argo\Indicator;

class IndicatorController extends Controller
{
    /**
     * The API to get highchart data
     *
     * @return \Illuminate\Http\Response
     */
    public function showApi($projectId, $indicatorId)
    {
        $indicator = Indicator::select([
                'title',
                'options',
                'yaxis',
                'xaxis_limit',
                'rule',
                'data_fields'
            ])
            ->findOrFail($indicatorId);

        $highchartObject = NULL;
        $indicatorService = new IndicatorService;
        if($indicator->rule == 'subproject') {
            $highchartObject = $indicatorService->generateComparisonHighchartObject(
                $indicator,
                $projectId
            );
        } else {
            $highchartObject = $indicatorService->generateHistoryHighchartObject(
                $indicator,
                $projectId
            );
        }

        return response($highchartObject, 200);
    }
}
