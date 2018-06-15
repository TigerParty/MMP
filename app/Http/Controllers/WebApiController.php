<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Exception;
use Log;
use Auth;
use Storage;
use Mail;
use Lang;

use App\Services\NotificationService;
use App\Services\ReportService;
use App\Argo\Report;
use App\Argo\Region;
use App\Argo\Attachment;

class WebApiController extends Controller
{
    public function getDistrictsByRegion(Request $request)
    {
        if(!$request->has('region_id'))
        {
            return response()->json([]);
        }

        $region_id = $request->input('region_id');

        $sub_regions = Region::where('parent_id', '=', $region_id)->get();

        return response()->json($sub_regions);
    }

    public function setRotateImage(Request $request)
    {
        $ath_id = $request->input('ath_id');
        $degree = $request->input('degree', -90);

        $rules = array(
            'ath_id' => 'required|integer',
            'degree' => 'integer'
        );

        $this->validate($request, $rules);

        try {
            $attach = Attachment::findOrFail($ath_id);

            if(!Storage::disk('local')->has($attach->path)) {
                throw new Exception("File Not Exists in path: $attach->path");
            }

            // Load
            if($attach->type == 'image/jpeg')
            {
                $image = imagecreatefromjpeg(storage_path('upload/').$attach->path);
            }
            elseif($attach->type == 'image/png')
            {
                $image = imagecreatefrompng(storage_path('upload/').$attach->path);
            }

            // Rotate
            $rotated_image = imagerotate($image, $degree, 0);

            // Output
            if($attach->type == 'image/jpeg')
            {
                imagejpeg($rotated_image, storage_path('upload/'.$attach->path));
            }
            elseif($attach->type == 'image/png')
            {
                imagepng($rotated_image, storage_path('upload/'.$attach->path));
            }

            // Regenerate sha1
            $new_sha1 = sha1_file(storage_path('upload/'.$attach->path));
            $new_path = substr($new_sha1, 0, 2).'/'.$new_sha1;
            Storage::move($attach->path, $new_path);
            $attach->path = $new_path;
            $attach->sha1 = $new_sha1;
            $attach->save();

            // Free the memory
            imagedestroy($image);
            imagedestroy($rotated_image);

            return response("OK", 200);
        } catch (Exception $e) {
            Log::error($e);
            abort(400);
        }
    }

    public function getNextReport(Request $request)
    {
        $project_id = $request->input('project_id');
        $origin_amount = $request->input('origin_amount');

        try
        {
            $report = Report::select(['report.id', 'report.project_id', 'report.updated_at'])
                ->with([
                    'images' => function($query) {
                        $query->orderBy('id', 'DESC');
                    }
                ])
                ->leftjoin('permission_level as vl', 'report.view_level_id', '=', 'vl.id')
                ->where('vl.priority', '>=', argo_current_permission())
                ->where('project_id', '=', $project_id)
                ->orderBy('updated_at', 'DESC')
                ->skip($origin_amount)
                ->take(1)
                ->get();

            return response(count($report) > 0 ? $report : 'no report', 200);
        }
        catch (Exception $e)
        {
            Log::error($e);
            abort(400);
        }
    }

    public function send_fb_comment_notification(Request $request)
    {
        if(!config('argodf.notification.fb_comment.enabled'))
        {
            abort(403);
        }

        $this->validate($request,
            [
                'comment_info.commentID' => 'required',
                'comment_info.message' => 'required',
                'comment_info.href' => 'required'
            ]
        );

        try
        {
            $data = array(
                'comment_info' => $request->input('comment_info')
            );

            NotificationService::sendFBCommentNotification($data);

            Log::info('A facebook comments notification email sent! Message: '.$request->comment_info['message'].', Page: '.$request->comment_info['href'].', Comment ID: '.$request->comment_info['commentID']);

            return response([], 200);
        }
        catch(Exception $e)
        {
            Log::error($e);

            abort(400);
        }
    }

    public function get_project_counter_region()
    {
        $regions = Region::with([
            'projects' => function($query) {
                $query->select(['project.id']);
            }
        ])
        ->whereNull('parent_id')
        ->get();

        return $regions;
    }

    public function auto_merge_project_value(Request $request)
    {
        $report_id = $request->report_id;

        $project = Report::select('project_id')
            ->find($report_id)
            ->project()
            ->with('edit_level')
            ->first(['id','edit_level_id']);

        if(!argo_is_accessible($project->edit_level->id))
        {
            abort(403);
        }

        $reportService = new ReportService($report_id);
        $reportService->autoMerge();

        return response('Auto merged', 200);
    }
}
