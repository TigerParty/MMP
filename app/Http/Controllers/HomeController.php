<?php
namespace App\Http\Controllers;

use DB;
use Log;

use App\Repositories\ProjectRepository;

use App\Argo\Chart;
use App\Argo\DynamicConfig;
use App\Argo\Project;
use App\Argo\Container;
use App\Argo\ProjectStatus;
use App\Argo\Region;
use App\Argo\ProjectValue;

class HomeController extends Controller
{
    public function home()
    {
        $rootContainerId = config('argodf.default_container_id');
        $rootContainer = Container::select('name')->find($rootContainerId);
        return view('home',[
            'container_name' => $rootContainer['name']
        ]);
    }

    public function homeApi(){
        $projectRepo = new ProjectRepository();
        $projects = $projectRepo->basicInfo()
            ->with(array('regions' => function($query){
                $query->orderBy('order', 'desc');
            }))
            ->whereIn('view_level_id', function($subQuery){
                $subQuery->select('id')
                    ->from('permission_level')
                    ->where('priority', '>=', argo_current_permission());
            })
            ->whereNotNull('cover_image_id')
            ->orderBy('updated_at', 'desc')
            ->take(4)
            ->get();

        $homepageChart = config('argodf.homepage_chart');

        if($homepageChart == 'project_status') {
            $projectStatus = ProjectStatus::select([
                    'project_status.name',
                    DB::raw('count(project.id) AS total')
                ])
                ->leftjoin('project', function($join){
                    $join->on('project.project_status_id', '=', 'project_status.id')
                        ->whereNull('project.deleted_at');
                })
                ->groupBy('project_status.id')
                ->get();
            $projectsCount = Project::count();
        } else if($homepageChart == 'project_count_group_region') {
            $projectStatus = Region::select([
                    'region.name',
                    DB::raw('count(project.id) AS total')
                ])
                ->leftJoin('relation_project_belongs_region', 'region.id', '=', 'relation_project_belongs_region.region_id')
                ->leftJoin('project', 'relation_project_belongs_region.project_id', '=', 'project.id')
                ->whereNull('region.parent_id')
                ->whereNull('project.parent_id')
                ->groupBy('region.id')
                ->get();
            $projectsCount = Project::whereNull('parent_id')->count();
        } else if( strpos($homepageChart, 'project_field_value:') === 0 ) {
            $targetFieldId = (int)explode(':', $homepageChart)[1];

            $projectStatus = ProjectValue::select([
                DB::raw('project_value.value AS name'),
                DB::raw('COUNT(project_value.project_id) AS total')
            ])
            ->leftJoin('project', 'project.id', '=', 'project_value.project_id')
            ->whereNull('project.deleted_at')
            ->where('project_value.form_field_id', '=', $targetFieldId)
            ->groupBy('project_value.value')
            ->orderBy('project_value.value')
            ->get();
            $projectsCount = Project::whereNull('parent_id')->count();
        } else {
            Log::warning('Unknown homepage_chart config');
            $projectStatus = [];
            $projectsCount = 0;
        }

        return response()->json([
            'projects' => $projects,
            'status' => $projectStatus,
            'project_count' => $projectsCount,
        ], 200, [], JSON_NUMERIC_CHECK);
    }

    public function featured_datas()
    {
        $indicator_chart_config = DynamicConfig::where('key', '=', 'indicator_charts')->first();

        $indicator_chart_ids = $indicator_chart_config ? json_decode($indicator_chart_config->value) : [];

        $indicator_charts = Chart::whereIn('id', $indicator_chart_ids)->get();

        foreach ($indicator_charts as $key => $chart) {
            $chart->selected_fields = json_decode($chart->selected_fields);
        }

        $array_data = array(
            'indicator_charts' => $indicator_charts,
        );

        return view('featured_datas', $array_data);
    }

    public function chart_gs()
    {
        return view('chart_gs');
    }

    public function showSitemap()
    {
        $projects = Project::select(['id', 'title', 'description', 'updated_at'])
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'DESC')->get();

        foreach ($projects as $project) {
            $project->url = asset("/project/$project->id");
            $project->priority = '1.0';
            $project->freq = 'weekly';
        }

        return response()->view('sitemap.show', [
            'projects' => $projects
        ])->header('Content-Type', 'text/xml');
    }

    public function tutorial()
    {
        return view('tutorial');
    }

    public function comment()
    {
        $array_data = array(
            'id' => 0,
            'type' => 'homepage'
        );
        return view('comment', $array_data);
    }
}
