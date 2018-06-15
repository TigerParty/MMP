<?php

namespace App\Http\Controllers\Management;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

use App\Argo\PermissionLevel;
use App\Argo\User;
use App\Argo\RegionLabel;

class UserController extends Controller
{
    const ITEMS_PER_PAGE = 30;

    public function query(Request $request)
    {
        $userOrm = User::selectRaw("
                user.id,
                user.name,
                user.email,
                user.permission_level_id,
                user.notification_enabled,
                permission_level.name AS role,
                IFNULL(ct.report_count, 0) AS report_count,
                DATE_FORMAT(submitted_date.last_submitted_at, '%Y-%m-%dT%TZ') AS last_submitted_at
            ")
            ->leftJoin('permission_level', 'user.permission_level_id', '=', 'permission_level.id')
            ->leftJoin(DB::raw('(
                SELECT MAX(created_at) AS last_submitted_at, created_by
                FROM report GROUP BY created_by ) submitted_date'
            ), 'user.id', '=', 'submitted_date.created_by')
            ->leftJoin(DB::raw('(
                SELECT COUNT(id) AS report_count, created_by
                FROM report GROUP BY created_by ) ct'
            ), 'user.id', '=', 'ct.created_by')
            ->where('permission_level.priority', '>', 1)
            ->limit(self::ITEMS_PER_PAGE)
            ->orderBy('user.name');

        if($request->has('permission_level_id'))
        {
            $userOrm->where(
                'permission_level_id',
                '=',
                $request->get('permission_level_id')
            );
        }

        if($request->has('keyword'))
        {
            $keywords = explode(" ", $request->get('keyword'));
            foreach($keywords as $keyword)
            {
                $userOrm->where('user.name', 'like', '%'.$keyword.'%');
            }
        }

        $userCountOrm = clone $userOrm;
        $userCount = $userCountOrm->count();

        if($request->has('page'))
        {
            $offset = ((int)$request->get('page') - 1) * self::ITEMS_PER_PAGE;
            $userOrm->skip($offset);
        }

        $users = $userOrm->get();

        $perms = PermissionLevel::select([
            'id',
            'name',
            'color'
        ])
        ->where('priority', '>', 1)
        ->get();

        return response()->json([
            'users' => $users,
            'user_count' => $userCount,
            'items_per_page' => self::ITEMS_PER_PAGE,
            'permissions' => $perms,
        ]);
    }

    public function show(Request $request, $userId)
    {
        $user = User::select([
                'id',
                'name',
                'email',
                'permission_level_id',
                'notification_enabled',
            ])
            ->with(['projects' => function($query){
                $query->select(['id', 'title']);
            }])
            ->findOrFail($userId);

        // Hide pivot locally for this API
        foreach($user->projects as $ownedProject)
        {
            unset($ownedProject->pivot);
        }

        $regionLabel = RegionLabel::select(['name'])
            ->orderBy('order', 'asc')
            ->get();

        return response()->json([
            'user' => $user,
            'region_label' => $regionLabel,
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string', // empty string not allowed
            'email' => 'required|email',
            'password' => 'required|string',
            'permission_level_id' => 'required|exists:permission_level,id',
        ]);

        $user = new User();
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->password = Hash::make($request->get('password'));
        $user->permission_level_id = $request->get('permission_level_id');

        $user->save();

        return response()->json(['status'=>'OK'], 200);
    }

    public function update(Request $request, $userId)
    {
        $request->validate([
            'name' => 'string', // empty string not allowed
            'email' => 'email',
            'password' => 'string',
            'permission_level_id' => 'exists:permission_level,id',
            'project_ids' => 'array',
        ]);

        $user = User::select([
                'id',
                'name',
                'email',
                'permission_level_id',
            ])
            ->with(['projects' => function($query){
                $query->select('id');
            }])
            ->findOrFail($userId);

        if($request->has('name'))
        {
            $user->name = $request->get('name');
        }

        if($request->has('email'))
        {
            $user->email = $request->get('email');
        }

        if($request->has('permission_level_id'))
        {
            $user->permission_level_id = $request->get('permission_level_id');
        }

        if($request->has('password'))
        {
            $user->password = Hash::make($request->get('password'));
        }

        if($request->has('project_ids'))
        {
            $user->projects()->sync($request->get('project_ids'));
        }

        $user->save();

        return response()->json([
            'status'=>'OK',
        ], 200);
    }

    public function switchNotify(Request $request, $userId)
    {
        $request->validate([
            'notification_enabled' => 'boolean|required',
        ]);
        $user = User::findOrFail($userId);

        $newNotifyFlag = filter_var(
            $request->get('notification_enabled'),
            FILTER_VALIDATE_BOOLEAN
        );

        if($newNotifyFlag == filter_var($user->notification_enabled, FILTER_VALIDATE_BOOLEAN))
        {
            return response()->json(['status'=>'Accepted'], 202);
        }

        $user->notification_enabled = $newNotifyFlag;
        $user->save();

        return response()->json(['status'=>'OK'], 200);
    }

    public function destroy(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $user->delete();
        return response()->json(['status'=>'deleted'], 200);
    }
}
