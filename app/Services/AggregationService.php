<?php

namespace App\Services;

use App\Argo\Aggregation;
use App\Argo\FormField;
use App\Argo\Project;
use DB;
use Illuminate\Database\Eloquent\Builder;

class AggregationService
{
    protected $parentId;
    protected $aggregationTypes = ['avg', 'count', 'ratio'];

    public function __construct($parentId = null)
    {
        $this->parentId = $parentId;
    }

    public function getAggregationValue(Aggregation $aggregation)
    {
        try {
            if (in_array($aggregation->type, $this->aggregationTypes)) {
                return $this->{$aggregation->type}(
                    $aggregation->target_container_id,
                    $aggregation->target_field_id,
                    $aggregation->filters
                );
            } else {
                throw new \Exception("Aggregation type not accepted");
            }
        } catch (\Exception $e) {
            \Log::error('[AggregationService]:' . $e);
            return NULL;
        }
    }

    private function avg($targetContainerId, $targetFieldId, $filters)
    {
        $subprojectIds = Project::select('id')
            ->where('container_id', '=', $targetContainerId);

        $subprojectIds = $this->filter($subprojectIds, $filters);

        $subprojectIds = $subprojectIds->lists('id');

        $average = DB::table('project_value')
            ->whereIn('project_id', $subprojectIds)
            ->where('form_field_id', $targetFieldId)
            ->avg('value');

        return round($average, 3);
    }

    private function filter(Builder $eloquentOrm, $filterConditions)
    {
        try {
            if ($this->parentId === NULL) {
                $eloquentOrm->whereNull('parent_id');
            } else {
                $eloquentOrm->where('parent_id', '=', $this->parentId);
            }

            if ($filterConditions) {
                $filterFieldIds = array_unique(array_pluck($filterConditions, 'field_id'));

                $arrangedFilterConditions = array();
                foreach ($filterFieldIds as $filterFieldId) {
                    $arrangedFilterConditions[$filterFieldId] = array_where($filterConditions, function ($key, $filter) use ($filterFieldId) {
                        return $filter['field_id'] == $filterFieldId;
                    });
                }

                foreach ($arrangedFilterConditions as $fieldId => $arrangedFilterConditions) {
                    if (gettype($fieldId) == 'integer') {
                        $eloquentOrm->leftJoin('project_value AS project_value_' . $fieldId, function ($join) use ($fieldId) {
                            $join->on('project_value_' . $fieldId . '.form_field_id', '=', DB::raw($fieldId))
                                ->on('project_value_' . $fieldId . '.project_id', '=', 'project.id');
                        });

                        $eloquentOrm->where(function ($query) use ($arrangedFilterConditions) {
                            foreach ($arrangedFilterConditions as $arrangedFilterCondition) {
                                if (gettype($arrangedFilterCondition['value']) == 'integer') {
                                    $query->orWhere('project_value_' . $arrangedFilterCondition['field_id'] . '.value', $arrangedFilterCondition['operator'], DB::raw($arrangedFilterCondition['value']));
                                } else {
                                    $query->orWhere('project_value_' . $arrangedFilterCondition['field_id'] . '.value', $arrangedFilterCondition['operator'], $arrangedFilterCondition['value']);
                                }
                            }
                        });
                    } else {
                        throw new \Exception("Filter's field id is not an integer");
                    }
                }
            }

            return $eloquentOrm;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function ratio($targetContainerId, $targetFieldId, $filters)
    {
        $aggregationFieldOptionsRaw = FormField::where('id', '=', $targetFieldId)
            ->pluck('options');
        $aggregationFieldOptions = $aggregationFieldOptionsRaw ? json_decode($aggregationFieldOptionsRaw) : [];

        $valueCounts = Project::select([
            'project_value.value',
            DB::raw('count(project_value.value) AS count')
        ])
            ->leftJoin('project_value', 'project_value.project_id', '=', 'project.id')
            ->where('container_id', '=', $targetContainerId)
            ->whereIn('project_value.value', $aggregationFieldOptions);

        $valueCounts = $this->filter($valueCounts, $filters);

        $valueCounts = $valueCounts->groupBy('value')
            ->get();

        $subprojectCount = $this->count($targetContainerId, $targetFieldId, $filters);
        if ($subprojectCount > 0) {
            $totalValueCount = 0;
            foreach ($valueCounts as $key => $valueCount) {
                $totalValueCount += $valueCount->count;
                $valueCount->percent = round($valueCount->count / $subprojectCount * 100, 0) . '%';
            }
            $valueCounts[] = [
                'value' => 'Others',
                'count' => $subprojectCount - $totalValueCount,
                'percent' => round(($subprojectCount - $totalValueCount) / $subprojectCount * 100, 0) . '%'
            ];
        }

        return $valueCounts;
    }

    private function count($targetContainerId, $targetFieldId, $filters)
    {
        $count = Project::where('container_id', '=', $targetContainerId);

        $count = $this->filter($count, $filters);

        $count = $count->count();

        return $count;
    }
}
