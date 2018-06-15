<?php
namespace App\Http\Api\SmsGateway\V1;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Log;
use DB;

use App\Argo\NotificationSMS;
use App\Argo\CitizenSMSReply;
use App\Argo\CitizenSMS;
use App\Argo\ReportRaw;


class AppController extends BaseController
{
    private function getEntityMessage($entityType, $entityId){
        switch ($entityType) {
            case 'App\\Argo\\Project':
                return "Please view the project here ".url("project/$entityId");
                break;

            case 'App\\Argo\\CitizenSMSReply':
                $reply = CitizenSMSReply::find($entityId);
                return $reply->message ? $reply->message : null;
                break;

            default:
                Log::error("Unknown entity type: $entityType");
                return "You got a notification from website, please view the website ".url('');
        }
    }

    public function fetch()
    {
        $scheduled = NotificationSMS::whereNull("submitted_at")
            ->orWhere(function($query) {
                 $query->where("schedule", "=", "daily")
                       ->whereRaw("TIMESTAMPDIFF(DAY, submitted_at, NOW()) >= 1");
            })
            ->orWhere(function($query) {
                 $query->where("schedule", "=", "weekly")
                       ->whereRaw("TIMESTAMPDIFF(WEEK, submitted_at, NOW()) >= 1");
            })
            ->orWhere(function($query) {
                 $query->where("schedule", "=", "monthly")
                       ->whereRaw("TIMESTAMPDIFF(MONTH, submitted_at, NOW()) >= 1");
            });

        $projectUpdated = NotificationSMS::select([
                 "notification_sms.*"
             ])
             ->leftJoin("project", function($join){
                  $join->on("project.id", "=","notification_sms.notify_id")
                       ->where("notification_sms.notify_type", "=", "App\Argo\Project");
             })
             ->where("notification_sms.schedule", "=", "by_update")
             ->whereNotNull("project.updated_at")
             ->whereRaw("TIMESTAMPDIFF(SECOND, notification_sms.submitted_at, project.updated_at) >= 1");

        $notifications = $projectUpdated->union($scheduled)
            ->get();

        $response = [];

        try {
            DB::beginTransaction();

            foreach ($notifications as $notification) {
                //-- update notification submitted_at to now when fetched
                $updatedNotificationSMS = NotificationSMS::findOrFail($notification->id);
                $updatedNotificationSMS->submitted_at = date("Y-m-d H:i:s");
                $updatedNotificationSMS->save();

                //-- arrange return list
                $obj = [];
                $obj['phone_number'] = $notification->phone_number;
                $obj['body'] = $this->getEntityMessage($notification->notify_type, $notification->notify_id);
                array_push($response, $obj);
            }

            DB::commit();
        }
        catch (Exception $e) {
            Log::error($e);
            DB::rollback();
            abort(400);
        }

        Log::info("ArgoGateway fetching from " . request()->ip() . ", record counts " . count($response));
        return response()->json($response, 200);
    }

    function submit(Request $request)
    {
        Log::info('SmsGateway API v1 received submissions');

        $message = $request->input('message');
        $phone = $request->input('from');

        $reportPayload = json_decode(base64_decode($message), true);

        // reportyPayload value based on manual: http://php.net/manual/en/function.json-decode.php
        if($reportPayload === null || $reportPayload === false || $reportPayload === true)
        {
            //Citizen SMS paintext
            $citizenSms = new CitizenSMS();
            $citizenSms->group_id = $request->get('group_id', 0);
            $citizenSms->phone_number = $phone;
            $citizenSms->submitted_at = Carbon::createFromTimestamp((int)$request->get('sent_timestamp')/1000)->toDateTimeString();
            $citizenSms->message = $message;
            $citizenSms->is_approved = 1; //Set 1 As Defalut

            $citizenSms->save();

            Log::info("SMS message store as citizen sms");
        }
        else
        {
            // Report SMS
            $reportRaw = new ReportRaw();
            $reportRaw->payload = json_encode($raw_data);
            $reportRaw->source = 'sms:v1';

            $reportRaw->save();

            Log::info("SMS message store as report");

            //TODO: Arrange Report Raw into citizen report or project report here
        }

    }
}
