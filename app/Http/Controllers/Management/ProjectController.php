<?php

namespace App\Http\Controllers\Management;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Argo\Project;

class ProjectController extends Controller
{
    const ITEMS_PER_PAGE = 30;

    public function query(Request $request)
    {
        $projectOrm = Project::selectRaw('
            id,
            title
        ');

        if($request->has('keyword'))
        {
            $keywords = explode(' ', $request->get('keyword'));

            foreach($keywords as $keyword)
            {
                $projectOrm->where('title', 'like', "%$keyword%");
            }
        }

        $projects = $projectOrm
            ->orderBy('title')
            ->get();

        return response()->json([
            'project' => $projects,
        ], 200);
    }
}
