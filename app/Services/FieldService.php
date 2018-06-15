<?php

namespace App\Services;

use App\Argo\Project;
use App\Argo\ProjectValue;
use App\Services\ProjectValueService;

class FieldService
{
    public function calculateDistance($projectId, $formFieldId)
    {
        $project = Project::leftjoin('relation_project_belongs_region as rpbr',
            'rpbr.project_id', '=', 'project.id')
            ->leftjoin('region as r',
                'r.id', '=', 'rpbr.region_id')
            ->leftjoin('region_label as rl',
                'rl.name', '=', 'r.label_name')
            ->where('project.id', '=', $projectId)
            ->whereNotNull('r.capital_lat')
            ->whereNotNull('r.capital_lng')
            ->whereNotNull('project.lat')
            ->whereNotNull('project.lng')
            ->orderBy('rl.order', 'desc')
            ->select(['project.id as projectId',
                'project.lat as projectLat',
                'project.lng as projectLng',
                'r.capital_lat as capitalLat',
                'r.capital_lng as capitalLng'])
            ->first();
        if ($project) {
            $dlat = $project['capitalLat'] - $project['projectLat'];
            $dlng = $project['capitalLng'] - $project['projectLng'];
            $distance = round(sqrt(pow($dlat, 2) + pow($dlng, 2)) * 111, 2);
            $project_value = new ProjectValue;
            $project_value->form_field_id = $formFieldId;
            $project_value->value = (string)$distance;

            $this->porjectValueService = new ProjectValueService();
            $this->porjectValueService->updateProjectValues(
                $project['projectId'],
                [$project_value]
            );
        }
    }
}
