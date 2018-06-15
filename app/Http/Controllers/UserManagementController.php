<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Argo\PermissionLevel;
use App\Argo\User;
use App\Argo\Project;
use App\Argo\Container;
use App\Argo\RegionLabel;

class UserManagementController extends Controller
{
    public function index()
    {
        $permission_levels = PermissionLevel::where('priority', '>', 1)->get();
        $rootContainerId = config('argodf.default_container_id');
        $rootContainer = Container::select('name')->find($rootContainerId);
        $regionLabels = RegionLabel::orderBy('order')
                    ->get()
                    ->pluck('name');

        return view('admin.user_management.index', [
                'permission_levels' => $permission_levels,
                'root_container' => $rootContainer,
                'region_labels' => $regionLabels
            ]);
    }

    public function get_users_by_filter(Request $request)
    {
        $users = User::with([
                'permission_level' => function($query){
                    $query->select(['id', 'name']);},
                'projects' => function($query){
                    $query->with(['regions'=> function($subquery){
                            $subquery->select(['name']);
                        }])
                      ->select([ 'id','title'])
                      ->whereNull('parent_id');
                }
            ]);

        if ($request->has('conditions.permission_level')) {
            $users = $users->where('permission_level_id', '=', $request->input('conditions.permission_level.id'));
        }

        if ($request->has('conditions.keywords')) {
            $keywords = explode(" ", $request->input('conditions.keywords'));
            foreach ($keywords as $keyword) {
                $users = $users->whereIn('id', function ($query) use ($keyword) {
                    $query->select('id')
                        ->from(with(new User)->getTable())
                        ->orWhere('name', 'LIKE', '%'.trim($keyword).'%');
                });
            }
        }

        $users = $users
        ->where('permission_level_id', '>', 1)
        ->orderBy('name')->get();

        return response(['users' => $users], 200);
    }

    public function get_project_by_region(Request $request)
    {
        $userId = $request->input('userId');
        $projects = Project::select(['id', 'title'])
                             ->whereDoesntHave('owners', function ($subQuery)use($userId){
                    $subQuery->where('user_id', $userId);
                })->whereNull('parent_id');


        if($request->input('regionId') >0 ){
           $projects->leftjoin('relation_project_belongs_region as rpbr',
                'project.id','=','rpbr.project_id')
                ->where('region_id', $request->input('regionId'));
        }

        $projects = $projects->get();

        return response(['projects' => $projects], 200);

    }

    public function add_user(Request $request)
    {
        $this->validator($request);

        try {
            \DB::beginTransaction();

            $new_user = new User;
            $new_user->name = $request->input('user.name');
            $new_user->email = $request->input('user.email');
            $new_user->password = bcrypt($request->input('user.password'));
            $new_user->permission_level_id = $request->input('user.permission_level.id');
            $new_user->save();

            \DB::commit();

            $new_user->permission_level = $request->input('user.permission_level');

            return response(['new_user' => $new_user], 200);
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error($e);
            abort(400);
        }
    }

    public function add_user_project(Request $request)
    {
        $userId = $request->input('userId');
        $projectId = $request->input('projectId');

        try {
            \DB::beginTransaction();

            User::findOrFail($userId)
                ->projects()
                ->attach($projectId);

            \DB::commit();

            \Log::info("Project ($projectId) attached to user ($userId) by admin user (" . session()->get('user_id') . ") from " . $request->ip());

            $project = Project::with(['regions'=> function($subquery){
                    $subquery->select(['name']);
                }])
                ->select([ 'id','title'])
                ->where('id', $request->input('projectId'))
                ->get();

            return response($project, 200);
        } catch (\Exception $e) {
            \Log::error($e);
            \DB::rollback();
            abort(400);
        }
    }

    public function delete_user_project(Request $request, $user_id, $project_id)
    {
        $user = User::findOrFail($user_id);

        try {
            \DB::beginTransaction();
            $user->projects()
                 ->detach($project_id);
            \DB::commit();

            \Log::info("Project ($project_id) detached from user ($user_id) by admin user (" . session()->get('user_id') . ") from " . $request->ip());

            return response([
                    'deleted_project_id' => $project_id,
                ], 200);
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error($e);
            abort(400);
        }
    }

    public function update_user(Request $request)
    {
        $this->validator($request);

        $update_user = User::findOrFail($request->input('user.id'));

        try {
            \DB::beginTransaction();

            $update_user->name = $request->input('user.name');
            $update_user->email = $request->input('user.email');
            if ($request->input('user.password_changed')) {
                $update_user->password = bcrypt($request->input('user.password'));
            }
            $update_user->permission_level_id = $request->input('user.permission_level.id');
            $update_user->save();

            \DB::commit();

            $self_updated = false;
            if ($update_user->id == \Auth::user()->id) {
                $self_updated = true;
                session(['identity' => $update_user->permission_level_id]);
            }

            return response([
                'update_user' => $update_user,
                'self_updated' => $self_updated
            ], 200);
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error($e);
            abort(400);
        }
    }

    public function delete_user(Request $request, $user_id)
    {
        $user = User::findOrFail($user_id);

        try {
            \DB::beginTransaction();

            $user->delete();

            $self_deleted = false;
            if ($user_id == \Auth::user()->id) {
                $self_deleted = true;
                session()->forget('identity');
            }

            \DB::commit();

            \Log::info("User ($user_id) deleted by admin user (" . session()->get('user_id') . ") from " . $request->ip() );

            return response([
                    'deleted_user_id' => $user_id,
                    'self_deleted' => $self_deleted
                ], 200);
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error($e);
            abort(400);
        }
    }

    private function validator($request)
    {
        $rules = [
            'user.name' => 'required|unique:user,name,'.$request->input('user.id'),
            'user.email' => 'nullable|email',
            'user.password' => 'nullable|min:6',
            'user.permission_level' => 'required',
            'user.permission_level.id' => 'nullable|integer|exists:permission_level,id'
        ];

        $messages = [
            'user.name.unique' => 'Duplicate user name.'
        ];

        return $this->validate($request, $rules, $messages);
    }
}
