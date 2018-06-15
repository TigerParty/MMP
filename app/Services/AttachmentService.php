<?php

namespace App\Services;

use App\Argo\Attachment;
use DB;
use Exception;
use Log;
use Storage;

class AttachmentService
{
    public function storeFile($file)
    {
        $origin_name = $file->getClientOriginalName();
        $sha1_name = sha1_file($file);
        $mime = $file->getMimeType();
        $folder_name = $this->getFolderName($sha1_name);
        $save_path = $folder_name . '/' . $sha1_name;
        $virus_scan_status = "not_scanned";

        if (config('services.virustotal.apikey')) {
            $virus_scan_status = $this->runVirusScan($sha1_name);
        }

        if (!Storage::disk('local')->has($save_path)) {
            Storage::put($save_path, file_get_contents($file));
        }

        $attachment_orm = Attachment::where('path', '=', $save_path)
            ->first();

        if (!$attachment_orm) {
            $attachment_orm = new Attachment();
            $attachment_orm->name = $origin_name;
            $attachment_orm->path = $save_path;
            $attachment_orm->type = $mime;
            $attachment_orm->sha1 = $sha1_name;
            $attachment_orm->status = $virus_scan_status;
            $attachment_orm->save();
        }

        return array(
            "id" => $attachment_orm->id,
            "name" => $attachment_orm->name,
            "mime" => $attachment_orm->type,
            "asset_path" => asset("/file/$attachment_orm->id"),
            "download_path" => asset("/file/$attachment_orm->id/download")
        );
    }

    private function getFolderName($sha1_name)
    {
        return $sub_dir = substr($sha1_name, 0, 2);
    }

    private function runVirusScan($sha1_name)
    {
        try {
            $virus_scan_status = "not_scanned";

            $virus_scan_result = json_decode(file_get_contents('https://www.virustotal.com/vtapi/v2/file/report?apikey=' . config('services.virustotal.apikey') . '&resource=' . $sha1_name));

            if (!is_null($virus_scan_result)) {
                switch ($virus_scan_result->response_code) {
                    case (1):
                        if ($virus_scan_result->positives > 0) {
                            $virus_scan_status = 'positives';
                        } else {
                            $virus_scan_status = 'negatives';
                        }
                        break;
                    default:
                        $virus_scan_status = "not_scanned";
                        $this->logVerboseMsg($virus_scan_result);
                        break;
                }
            }

            return $virus_scan_status;
        } catch (Exception $e) {
            return "not_scanned";
        }
    }

    private function logVerboseMsg($result)
    {
        if ($result->verbose_msg) {
            Log::info('Upload file: get file scanned report on virustotal fail, message: ' . $result->verbose_msg);
        }
    }
}
