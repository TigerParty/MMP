<?php

namespace App\Http\Api\LiteApp\V1;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

use App\Repositories\ReportCitizenRepository;
use App\Argo\ReportCitizen;
use App\Services\ReportArrangeService;
use App\Services\AttachmentService;

class AppController extends BaseController
{
    // TODO: Centeralize the upload file help cross App APIs
    private function uploadFile($files, $metaOnFileName = array())
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(900);

        $pivotOnAttachId = array();

        $attachService = new AttachmentService();
        foreach ($files as $file) {
            $attachment = $attachService->storeFile($file);
            $attachName = $attachment['name'];
            $attachId = $attachment['id'];
            $metaOnAttachName = array_get($metaOnFileName, $attachName, array());
            $pivotOnAttachId[$attachId] = array(
                'attached_at' => array_get($metaOnAttachName, "attached_at", new Carbon('now')),
                'attached_form_id' => array_get($metaOnAttachName, "attached_form_id", null),
                'description' => json_encode(array(
                    'header' => array_get($metaOnAttachName, "header", ""),
                    'content' => array_get($metaOnAttachName, "content", ""),
                    'lat' => array_get($metaOnAttachName, "lat", ""),
                    'lng' => array_get($metaOnAttachName, "lng", ""),
                )),
            );
        }

        return $pivotOnAttachId;
    }

    private function getReportCitizen($meta)
    {
        return ReportCitizen::where('meta', 'LIKE', '%"device_id":"' . $meta['device_id'] . '"%')
        ->where('meta', 'LIKE', '%"local_id":' . $meta['local_id'] . '%')
        ->firstOrCreate(array());
    }

    public function submit(Request $request)
    {
        try {
            $attachments = array();
            $meta = array();

            $files = $request->file();
            $info = json_decode($request->input('info'), true);

            //-- Save raw input to report_raw table
            $reportRawOrm = ReportArrangeService::saveRawReport($info, "http");
            $attachments = $this->uploadFile($files);
            $reportRawOrm->attachments()->sync($attachments);

            // -- Arrange report info
            $type = array_get($info, 'type', 'unknown');
            $version = array_get($info, 'version', 'unknown');
            $payload = array_get($info, 'payload', array());

            // -- Store report
            if ($type == 'citizen') {
                $meta = array(
                    'local_id' => array_get($payload, 'id', null),
                    'device_id' => array_get($payload, 'device_id', null),
                    'first_name' => array_get($payload, 'first_name', null),
                    'last_name' => array_get($payload, 'last_name', null),
                );
                try {
                    DB::beginTransaction();
                    $report = $this->getReportCitizen($meta);
                    $report->email = array_get($payload, 'email', null);
                    $report->phone = array_get($payload, 'phone', null);
                    $report->comment = array_get($payload, 'comment', null);
                    $report->source = 'http';
                    $report->lat = array_get($payload, 'lat', null);
                    $report->lng = array_get($payload, 'lng', null);
                    $report->updated_at = new Carbon(array_get($payload, 'updated_at', 'now'));
                    $report->meta = $meta;
                    $report->version = $version;
                    $report->save();
                    $report->attachments()->sync($attachments);
                    DB::commit();
                } catch (Exception $e) {
                    DB::rollback();
                    throw $e;
                }
            } else {
                throw new Exception("Got unknown report format");
            }
            return response()->json(array('result' => 'OK'), 200)->header("Content-Type", "text/html");
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(array('result' => 'error'), 400)->header("Content-Type", "text/html");
        }
    }
}
