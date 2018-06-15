<?php

namespace App\Http\Controllers\Feedback;


use Illuminate\Support\Facades\DB;
use App\Argo\CitizenSMS;
use App\Argo\CitizenSMSReply;
use App\Argo\NotificationSMS;
use App\Http\Controllers\Controller;
use App\Http\Requests\CitizenSMSReply\StoreRequest;

class CitizenSMSController extends Controller
{
    const SMS_ITEMS_PER_PAGE = 10;

    function smsIndexApi()
    {
        $latestSmsQuery = CitizenSMS::select([
            'phone_number',
            DB::raw('MAX(id) AS max_id'),
        ])->whereNull('deleted_at')
            ->groupBy([
                'phone_number'
            ])->getQuery();

        $queryBuilderSMS = DB::table(DB::raw("(" . $latestSmsQuery->toSql() . ") AS latestSmsQuery"))
            ->mergeBindings($latestSmsQuery)
            ->select([
                'citizen_sms.id',
                'citizen_sms.phone_number',
                'citizen_sms.message',
                'citizen_sms.is_read',
                'citizen_sms.created_at'
            ])
            ->leftJoin('citizen_sms', function ($join) {
                $join->on('citizen_sms.phone_number', '=', 'latestSmsQuery.phone_number')
                    ->on('citizen_sms.id', '=', 'latestSmsQuery.max_id');
            })
            ->orderBy('created_at', 'DESC');

        //-- new amount
        $queryBuilderNewAmountSMS = clone $queryBuilderSMS;
        $newAmount = $queryBuilderNewAmountSMS->where('is_read', 0)->count();

        //-- responded amount
        $queryBuilderRespondedAmountSMS = clone $queryBuilderSMS;
        $respondedAmount = $queryBuilderRespondedAmountSMS->whereRaw('(SELECT EXISTS(
                    SELECT citizen_sms_reply.id
                    FROM citizen_sms_reply
                    WHERE citizen_sms_reply.citizen_sms_id = citizen_sms.id
                    AND citizen_sms.created_at < citizen_sms_reply.created_at)
                LIMIT 1)')->count();

        //-- paginated
        $paginated = $queryBuilderSMS->paginate(self::SMS_ITEMS_PER_PAGE);

        //-- convert to citizenSms model object
        $data = CitizenSMS::hydrate($paginated->items());

        return response()->json([
            'is_admin' => argo_is_admin_accessible(),
            'new_amount' => $newAmount,
            'responded_amount' => $respondedAmount,
            'total_amount' => $paginated->total(),
            'sms' => $data,
            'per_page' => $paginated->perPage()
        ]);
    }

    function smsShowApi($id)
    {
        $citizenSMS = CitizenSMS::find($id);

        $receivedSmsQuery = CitizenSMS::select([
            DB::raw('"received" AS type'),
            'phone_number',
            'message',
            'created_at'
        ])->where('phone_number', $citizenSMS->phone_number);

        $data = CitizenSMS::select([
            DB::raw('"reply" AS type'),
            'citizen_sms.phone_number AS phone_number',
            'citizen_sms_reply.message AS message',
            'citizen_sms_reply.created_at AS created_at'
        ])->join('citizen_sms_reply', function ($join) {
            $join->on('citizen_sms_reply.citizen_sms_id', '=', 'citizen_sms.id');
        })->where('phone_number', $citizenSMS->phone_number)
            ->union($receivedSmsQuery)
            ->orderBy('created_at', 'ASC')
            ->get();

        return response()->json($data);
    }

    function smsDeleteApi($id)
    {
        $citizenSMS = CitizenSMS::find($id);

        try {
            CitizenSMS::where('phone_number', $citizenSMS->phone_number)->delete();

            return response()->json(true);
        } catch (\Exception $e) {
            return response()->json("Delete failed", 400);
        }
    }

    function smsMarkReadApi($id)
    {
        $citizenSMS = CitizenSMS::find($id);

        try {
            $sms = CitizenSMS::where('phone_number', $citizenSMS->phone_number)
                ->where('is_read', false)
                ->update([
                    'is_read' => true
                ]);

            return response()->json($sms);
        } catch (\Exception $e) {
            return response()->json("Mark read for sms failed", 400);
        }
    }


    function smsReplyStoreApi(StoreRequest $request)
    {
        $citizenSms = CitizenSMS::findOrFail($request->get('citizen_sms_id'));

        try {
            $smsReply = CitizenSMSReply::create($request->all());

            $notifySms = new NotificationSMS();
            $notifySms->phone_number = $citizenSms->phone_number;
            $notifySms->schedule = 'once';
            $notifySms->notify_id = $smsReply['id'];
            $notifySms->notify_type = 'App\\Argo\\CitizenSMSReply';
            $notifySms->save();

            return response()->json($smsReply);
        } catch (\Exception $e) {
            return response()->json("Save reply sms failed", 400);
        }
    }
}