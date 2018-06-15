<?php

namespace App\Http\Controllers;

use DB;
use Log;
use Illuminate\Http\Request;
use App\Argo\ProjectStatus;

class StatusController extends Controller
{
    public function index()
    {
        $status_labels = ProjectStatus::orderBy('id', 'DESC')->get();

        $array_data = array(
                'status_labels' => $status_labels
            );

        return view('status.index', $array_data);
    }

    public function update(Request $request)
    {
        if (!argo_is_accessible(config('argodf.admin_function_priority')))
        {
            abort(403);
        }

        $status_changes = $request->changes;

        $rules = array();

        foreach ($status_changes['created_status'] as $key => $value) {
            $rules['changes.created_status.'.$key.'.name'] = 'required';
        }

        foreach ($status_changes['updated_status'] as $key => $value) {
            $rules['changes.updated_status.'.$key.'.id'] = 'required|integer';
            $rules['changes.updated_status.'.$key.'.name'] = 'required';
        }

        $rules['changes.deleted_status_ids'] = 'array';

        $this->validate($request, $rules);

        try
        {
            DB::beginTransaction();

            ProjectStatus::where('default', '=', 1)->update(['default' => 0]);

            ProjectStatus::insert(array_reverse($status_changes['created_status']));

            foreach ($status_changes['updated_status'] as $index => $status) {
                ProjectStatus::find($status['id'])->update(['name' => $status['name'], 'default' => $status['default']]);
            }

            ProjectStatus::destroy($status_changes['deleted_status_ids']);

            DB::commit();

            return response($status_changes, 200);
        }
        catch(\Exception $e)
        {
            DB::rollback();
            Log::error($e);
            abort(400);
        }
    }
}
