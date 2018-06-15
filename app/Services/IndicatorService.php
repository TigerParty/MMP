<?php

namespace App\Services;

use App\Argo\FormField;
use App\Argo\Indicator;
use App\Argo\ProjectValue;
use App\Argo\Report;
use App\Argo\ReportValue;
use Carbon\Carbon;
use DB;

class IndicatorService
{
    private $timePeriodDateFormats = [
        'year' => 'Y',
        'month' => 'Y M',
    ];
    private $mysqlDateFormats = [
        'year' => '%Y',
        'month' => '%Y %b',
    ];

    public function generateHistoryHighchartObject(Indicator $indicator, $entityId)
    {
        try {
            $xAxisRule = $indicator->rule;

            $timePeriods = [];
            $timePeriodsDisplay = [];
            if (in_array($xAxisRule, ['year', 'month'])) {
                if ($xAxisRule == 'year') {
                    $timeBegin = Carbon::now()->modify((int)-($indicator->xaxis_limit - 1) . ' year');
                } else if ($xAxisRule == 'month') {
                    $timeBegin = Carbon::now()->modify((int)-($indicator->xaxis_limit - 1) . ' month');
                }
                for ($i = $indicator->xaxis_limit - 1; $i >= 0; $i--) {
                    $now = Carbon::now();
                    if ($xAxisRule == 'year') {
                        $offset = $now->modify((int)-($i) . ' year');
                    } else if ($xAxisRule == 'month') {
                        $offset = $now->modify((int)-($i) . ' month');
                    }
                    array_push($timePeriods, $offset);
                    array_push($timePeriodsDisplay, $offset->format($this->timePeriodDateFormats[$xAxisRule]));
                }
            } else {
                throw new \Exception("Unknown history chart frequency: $xAxisRule");
            }

            $series = [];
            foreach ($indicator->data_fields as $fieldId) {
                $yAxisValueQuery = $this->queryYAxisValuesByReport(
                    $entityId,
                    $fieldId,
                    $timeBegin,
                    $xAxisRule
                );
                $yAxisValues = $yAxisValueQuery['yAxisValues'];

                $data = [];
                foreach ($timePeriods as $key => $timePeriod) {
                    $yAxisValue = null;
                    if (array_key_exists($timePeriod->format($this->timePeriodDateFormats[$xAxisRule]), $yAxisValues)) {
                        $yAxisValue = (int)$yAxisValues[$timePeriod->format($this->timePeriodDateFormats[$xAxisRule])];
                    }
                    array_push($data, $yAxisValue);
                }
                array_push($series, [
                    'name' => $yAxisValueQuery['formFieldName'],
                    'data' => $data,
                ]);
            }

            $highchartObject = [
                'title' => $indicator->title,
                'options' => $indicator->options,
                'xAxis' => [
                    'categories' => $timePeriodsDisplay
                ],
                'yAxis' => $indicator->yaxis,
                'series' => $series,
            ];

            return $highchartObject;
        } catch (\Exception $e) {
            \Log::error($e);
            return NULL;
        }
    }

    private function queryYAxisValuesByReport($entityId, $formFieldId, $timeBegin, $xAxisRule)
    {
        $yAxisValues = ReportValue::select([
            'report_value.value',
            DB::raw("DATE_FORMAT(report.updated_at, '" . $this->mysqlDateFormats[$xAxisRule] . "') AS time_period")
        ])
            ->leftJoin('report', 'report_value.report_id', '=', 'report.id')
            ->where('report.project_id', '=', $entityId)
            ->whereIn('report.updated_at', Report::select([
                DB::raw('max(updated_at) AS lastest_updated_at')
            ])
                ->leftJoin('report_value', 'report.id', '=', 'report_value.report_id')
                ->where('report.project_id', '=', $entityId)
                ->where('updated_at', '>', DB::raw("DATE_FORMAT('$timeBegin', '" . $this->mysqlDateFormats[$xAxisRule] . "')"))
                ->where('report_value.form_field_id', '=', $formFieldId)
                ->groupBy(DB::raw("DATE_FORMAT(updated_at, '" . $this->mysqlDateFormats[$xAxisRule] . "')"))
                ->lists('lastest_updated_at')
            )
            ->where('form_field_id', '=', $formFieldId)
            ->groupBy('time_period')
            ->get();

        $formFieldName = FormField::where('id', '=', $formFieldId)->pluck('name');

        $yAxisValues = array_pluck($yAxisValues, 'value', 'time_period');

        return array(
            'yAxisValues' => $yAxisValues,
            'formFieldName' => $formFieldName,
        );
    }

    public function generateComparisonHighchartObject(Indicator $indicator, $entityId)
    {
        try {
            $xAxisRule = $indicator->rule;

            $series = [];
            $categories = [];
            $allYAxisValues = [];
            foreach ($indicator->data_fields as $fieldId) {
                $yAxisValueQuery = $this->queryYAxisValuesBySubproject(
                    $entityId,
                    $fieldId,
                    $indicator->xaxis_limit
                );
                $yAxisValues = $yAxisValueQuery['yAxisValues'];

                $fieldCategories = array_pluck($yAxisValues, 'title');
                $extraCategories = array_diff($fieldCategories, $categories);
                $categories = array_merge($categories, $extraCategories);

                $allYAxisValues[$yAxisValueQuery['formFieldName']] = array_pluck($yAxisValues, 'value', 'title');
            }
            $categories = array_sort_recursive($categories);

            foreach ($allYAxisValues as $fieldName => $fieldData) {
                $dataset = [];
                foreach ($categories as $category) {
                    if (array_key_exists($category, $fieldData)) {
                        array_push($dataset, (int)$fieldData[$category]);
                    } else {
                        array_push($dataset, 0);
                    }
                }
                array_push($series, [
                    'name' => $fieldName,
                    'data' => $dataset
                ]);
            }

            $highchartObject = [
                'title' => $indicator->title,
                'options' => $indicator->options,
                'xAxis' => [
                    'categories' => $categories,
                ],
                'yAxis' => $indicator->yaxis,
                'series' => $series,
            ];

            return $highchartObject;
        } catch (\Exception $e) {
            \Log::error($e);
            return NULL;
        }
    }

    private function queryYAxisValuesBySubproject($entityId, $formFieldId, $limit)
    {
        $yAxisValues = ProjectValue::select([
            'project.title',
            'project_value.value',
        ])
            ->leftJoin('project', 'project_value.project_id', '=', 'project.id')
            ->where('project.parent_id', '=', $entityId)
            ->where('project_value.form_field_id', '=', $formFieldId)
            ->orderBy(DB::raw('LENGTH(project.title)'))
            ->orderBy('project.title')
            ->take($limit)
            ->get();

        $formFieldName = FormField::where('id', '=', $formFieldId)->pluck('name');

        return array(
            'yAxisValues' => $yAxisValues,
            'formFieldName' => $formFieldName,
        );
    }
}
