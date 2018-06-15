<?php
namespace App\Repositories;

use DB;
use App\Argo\Container;

class ContainerRepository {
    private $recursiveLimit = 10;

    public function getSubContainersWithCoverById($projectId, $containerId)
    {
        $subcontainers = Container::select([
            'container.id',
            'container.name',
            'container.default_cover_image_id',
            'relation_project_image_container.cover_image_id',
            'relation_project_image_container.project_id'
        ])
            ->leftJoin('relation_project_image_container', function($join) use ($projectId){
                $join->on(
                    'relation_project_image_container.container_id',
                    '=',
                    'container.id')
                    ->on(
                        'relation_project_image_container.project_id',
                        '=',
                        DB::raw(app('db')->getPdo()->quote($projectId))
                    );
            })
            ->where(
                DB::raw('COALESCE(container.parent_id)'),
                '=',
                DB::raw('COALESCE('.app('db')->getPdo()->quote($containerId).')')
            )
            ->get();

        $result = array();
        foreach($subcontainers as $subcontainer)
        {
            $cover_image_id = $subcontainer->cover_image_id ? $subcontainer->cover_image_id : $subcontainer->default_cover_image_id;
            array_push($result, array(
                'id' => $subcontainer->id,
                'name' => $subcontainer->name,
                'path' => url("project/$projectId/container/$subcontainer->id"),
                'cover_image_path' => argo_image_path($cover_image_id)
            ));
        }

        return $result;
    }

    public function getFlattenSubContainers($containerId)
    {
        $containers = Container::select([
            'container.id',
            'container.name',
            'container.parent_id',
            'container.form_id',
            'container.reportable',
        ])
            ->get()
            ->toArray();

        $subcontainers = $this->getSubContainers($containerId, $containers, [], 0);

        return $subcontainers;
    }

    private function getSubContainers($parentId, $containers, $result, $deepth)
    {
        if ($deepth > $this->recursiveLimit) {
            \Log::warning("Recursive function getFlatSubContainers() exceed maximum deepth $this->recursiveLimit! break recrsive");
            return;
        }

        $deepth += 1;

        $subContainers = array_where($containers, function($container, $index) use ($parentId) {
            return $container['parent_id'] == $parentId;
        });

        foreach($subContainers as $subContainer) {
            // If reportable, give self, else give sub-tree
            if ($subContainer['reportable'] == 1) {
                array_push($result, $subContainer);
            } else {
                $subTree = $this->getSubContainers($subContainer['id'], $containers, $result, $deepth);
                $result += $subTree;
            }
        }

        return $result;
    }
}
