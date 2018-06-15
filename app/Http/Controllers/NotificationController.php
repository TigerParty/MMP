<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Exception;
use Log;
use Validator;

use App\Services\NotificationService;
use App\Services\RegionService;

use App\Argo\Notification;
use App\Argo\NotificationSMS;
use App\Argo\Project;
use App\Argo\Report;
use App\Argo\RegionLabel;
use App\Argo\CitizenSMSReply;

class NotificationController extends Controller {

    public function index()
    {
        return view('admin.notification');
    }

    public function indexApi()
    {
        $regionService = new RegionService();

        $regionLabels = RegionLabel::orderBy('order')
            ->get();

        $projects = Project::with(['regions'])
            ->get([
                'id',
                'title'
            ]);

        foreach ($projects as $project) {
            $arrangedRegions = $regionService->arrangeRegionGroupByLabel($project->regions, $regionLabels);
            unset($project->regions);
            $project->regions = $arrangedRegions;
        }

        return response()->json([
            'region_labels' => $regionLabels,
            'projects' => $projects,
        ], 200, [], JSON_NUMERIC_CHECK);
    }

    public function indexQueryApi(Request $request)
    {
        $regionService = new RegionService();
        $conditions = $request->input('conditions', []);

        $regionLabels = RegionLabel::orderBy('order')
            ->get();

        $project_orm = Project::with(['regions']);

        $queryKeyword = array_get($conditions, 'keyword', false);
        if ($queryKeyword) {
            $project_orm->where('title', "like", "%$queryKeyword%");
        }

        $queryRegionIds = array_get($conditions, 'regions', false);
        if ($queryRegionIds) {
            foreach ($queryRegionIds as $queryRegionId) {
                $project_orm->whereHas('regions', function ($query) use ($queryRegionId) {
                    $query->where('id', '=', $queryRegionId);
                });
            }
        }

        $order = $request->input('order', false);
        if ($order == "title") {
            $project_orm->orderBy($order, 'ASC');
        } elseif ($order == "updated_at") {
            $project_orm->orderBy($order, 'DESC');
        }

        $projects = $project_orm->get(['id', 'title']);

        foreach ($projects as $project) {
            $arrangedRegions = $regionService->arrangeRegionGroupByLabel($project->regions, $regionLabels);
            unset($project->regions);
            $project->regions = $arrangedRegions;
        }

        return response()->json([
            'region_labels' => $regionLabels,
            'projects' => $projects,
        ], 200, [], JSON_NUMERIC_CHECK);
    }

    public function showEmail($notificationType = null, $notificationId = null){
        $instance = array();
        switch ($notificationType) {
            case 'project':
                $instance = Project::findOrFail($notificationId);
                break;

            default:
                abort(400);
                break;
        }

        $notifications = $instance->notifications()->get();

        return response()->json([
            'emails' => $notifications
        ], 200, [], JSON_NUMERIC_CHECK);
    }

    public function syncEmail(Request $request, $notificationType, $notificationId){
        $input_noticiations = $request->input('emails', array());
        $instance = null;

        switch ($notificationType) {
            case 'project':
                $instance = Project::findOrFail($notificationId);
                break;

            default:
                abort(400);
                break;
        }

        if($instance != null){
            try {
                DB::beginTransaction();

                foreach ($instance->notifications()->get() as $notification) {
                    $notification->delete();
                }

                foreach ($input_noticiations as $input_notification) {
                    $new_notification = new Notification();
                    $new_notification->receiver = $input_notification['receiver'];
                    $new_notification->email = $input_notification['email'];
                    $new_notification->schedule = $input_notification['schedule'];

                    $instance->notifications()->save($new_notification);
                }

                DB::commit();
                return response()->json([] , 200);
            }
            catch (Exception $e) {
                DB::rollback();
                abort(400);
            }
        }
        else{
            abort(400);
        }
    }

    public function showSMS($notificationType = null, $notificationId = null){
        $instance = array();
        switch ($notificationType) {
            case 'project':
                $instance = Project::findOrFail($notificationId);
                break;

            default:
                abort(400);
                break;
        }

        $notifications = $instance->notification_smses()->get();

        return response()->json([
            'smses' => $notifications
        ], 200, []);
    }

    public function syncSMS(Request $request, $notificationType, $notificationId){
        $input_noticiations = $request->input('SMSes', array());
        $instance = null;

        switch ($notificationType) {
            case 'project':
                $instance = Project::findOrFail($notificationId);
                break;

            default:
                abort(400);
                break;
        }

        if($instance != null){
            try {
                DB::beginTransaction();

                foreach ($instance->notification_smses()->get() as $notification) {
                    $notification->delete();
                }

                foreach ($input_noticiations as $input_notification) {
                    $new_notification = new NotificationSMS();
                    $new_notification->receiver = $input_notification['receiver'];
                    $new_notification->phone_number = $input_notification['phone_number'];
                    $new_notification->schedule = $input_notification['schedule'];

                    $instance->notification_smses()->save($new_notification);
                }

                DB::commit();
                return response()->json([] , 200);
            }
            catch (Exception $e) {
                DB::rollback();
                abort(400);
            }
        }
        else{
            abort(400);
        }
    }

    public function store(Request $request){
        $notification_info = $request->input('notification', array(
                                                            'receiver' => '',
                                                            'email' => '',
                                                            'schedule' => '',
                                                            'report_id' => ''
                                                        ));

        $rules = array( 'notification.receiver'  => 'required| string',
                        'notification.email' => 'required| email',
                        'notification.schedule' => 'required| string| in:daily,weekly,monthly',
                        'notification.report_id' => 'required| exists:report,id'
                        );

        $this->validate($request, $rules);

        try
        {
            DB::beginTransaction();
            $new_notification = new Notification();
            $new_notification->receiver = $notification_info['receiver'];
            $new_notification->email = $notification_info['email'];
            $new_notification->schedule = $notification_info['schedule'];

            $notification_report = Report::findOrFail($notification_info['report_id']);
            $new_notification = $notification_report->notifications()->save($new_notification);
            DB::commit();

            if (config('argodf.notification.scheduled_report.enabled'))
            {
                $project = Project::find($notification_report['project_id']);
                $report_url = asset('/project/'.$notification_report['project_id'].'/report/'.$notification_report['id']);
                $subject = 'Notification for Report -'.$notification_report['title'].':'.$project['title'];
                $data = array(
                    'url'=> $report_url
                );

                NotificationService::sendScheduledReportNotification($notification_info['email'], $notification_info['receiver'], $subject, $data);
            }

            $notification = array(
                    'id' => $new_notification['id'],
                    'receiver' => $new_notification['receiver'],
                    'email' => $new_notification['email'],
                    'schedule' => $new_notification['schedule'],
                    'notify_id' => $new_notification['notify_id']
                );

            return response()->json($notification);
        }
        catch(Exception $e)
        {
            Log::error('Store Notification Error:'.$e);
            DB::rollback();
            return abort(400);
        }
    }

    public function destroy($notification_id){
        try
        {
            $rules = array('notification_id' => 'required|exists:notification,id');
            $validator = Validator::make(array('notification_id' => $notification_id), $rules);
            if($validator->fails())
            {
                throw new Exception("Notifiaction Remove Validator Fails");
            }
        }
        catch(Exception $e)
        {
            Log::error('Notification Remove Catch Exception: '.$e);
            return abort(400);
        }

        try
        {
            DB::beginTransaction();
            Notification::find($notification_id)->delete();
            DB::commit();
            return response()->json($notification_id);
        }
        catch(Exception $e)
        {
            Log::error('Remove Notification Error:'.$e);
            DB::rollback();
            return abort(400);
        }
    }
}
