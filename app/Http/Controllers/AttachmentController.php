<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use DB;
use Storage;
use Log;
use Exception;
use Validator;
use ZipArchive;
use File;

use App\Argo\Attachment;

class AttachmentController extends Controller {
    const MIME_TYPE_ZIP = "application/zip";

    const VIRUSTOTAL_RESULT_NOT_SCANNED = "not_scanned";
    const VIRUSTOTAL_RESULT_POSITIVES = "positives";
    const VIRUSTOTAL_RESULT_NEGATIVES = "negatives";

    /**
     * Get file from a post $_POST['file']
     *
     * @return JSON with file id
     */
    public function checkFile(Request $request)
    {
        if($request->hasFile('file'))
        {
            $this->validate($request, [
                    'file' => 'max:32768'
                ]);

            return $this->doUpload($request->file('file'));
        }

        Log::warning("File upload fail, forgot to setup upload_max_filesize or post_max_size?");
        return response()->json(array(), 400);
    }

    public function doUpload($file)
    {
        $origin_name = $file->getClientOriginalName();
        $sha1_name = sha1_file($file);
        $folder_name = substr($sha1_name, 0, 2);
        $mime = $file->getMimeType();

        //-- Make sure virus_scan_status exists if the virus scan function disabled
        $virus_scan_status = "not_scanned";

        $save_path_name = $folder_name.'/'.$sha1_name;

        if(Storage::disk('local')->has($save_path_name))
        {
            $exist_attach = Attachment::where('path', '=', $save_path_name)->first();

            if($exist_attach)
            {
                Log::warning("Uploading exist file: [$exist_attach->id] $save_path_name");

                return response()->json($this->getResponseArray($exist_attach));
            }
        }

        try
        {
            if(config('services.virustotal.apikey'))
            {
                $virus_scan_result = json_decode(file_get_contents('https://www.virustotal.com/vtapi/v2/file/report?apikey='.config('services.virustotal.apikey').'&resource='.$sha1_name));

                if($virus_scan_result != NULL)
                {
                    if($virus_scan_result->response_code == 1)
                    {
                        if($virus_scan_result->positives > 0)
                        {
                            $virus_scan_status = 'positives';
                        }
                        else
                        {
                            $virus_scan_status = 'negatives';
                        }
                    }
                    else
                    {
                        if($virus_scan_result->verbose_msg)
                        {
                            Log::info('Upload file: get file scanned report on virustotal fail, message:'.$virus_scan_result->verbose_msg);
                        }
                        $virus_scan_status = 'not_scanned';
                    }
                }
                else
                {
                    $virus_scan_status = 'not_scanned';
                }
            }

            DB::beginTransaction();

            //-- move file
            Storage::put($save_path_name, file_get_contents($file));

            $attach = new Attachment();
            $attach->name = $origin_name;
            $attach->path = $save_path_name;
            $attach->type = $mime;
            $attach->sha1 = $sha1_name;
            $attach->status = $virus_scan_status;
            $attach->save();

            DB::commit();

            return response()->json($this->getResponseArray($attach));
        }
        catch(Exception $e)
        {
            DB::rollback();

            Log::error($e);

            abort(400);
        }
    }

    public function upload(Request $request)
    {
        $fileObj = $request->file('file');

        $validator = Validator::make($request->all(), [
            'file' => 'required|max:32000'
        ]);

        if ($validator->fails() || !$fileObj->isValid()) {
            return response()->json("File is invalid", 400);
        }

        $fileMimeType = $fileObj->getMimeType();

        $extractTargetPath = "";
        $unprocessedFiles = array();
        //-- process zip file
        if ($fileMimeType == self::MIME_TYPE_ZIP) {
            try {
                $zip = new ZipArchive;
                if (!$zip->open($fileObj)) {
                    throw new Exception("Error reading zip file");
                }

                $extractTargetPath = storage_path().'/temp/'.time();
                if (!File::makeDirectory($extractTargetPath)) {
                    throw new Exception("Error making diractory.");
                }

                if (!$zip->extractTo($extractTargetPath)){
                    throw new Exception("Error extracting zip file");
                }

                $zip->close();
                unlink($fileObj);

                Log::info("Zip file has been extracted to $extractTargetPath");
            }
            catch (Exception $e) {
                Log::error($e->getMessage());

                if (File::isDirectory($extractTargetPath)){
                    File::deleteDirectory($extractTargetPath);
                }
                return response()->json("Error uploading zip file", 400);
            }

            //-- create File object from path
            $files = File::files($extractTargetPath);
            foreach ($files as $filePath) {
                array_push($unprocessedFiles, $this->getFileObjectFromPath($filePath));
            }
        }
        else {
            array_push($unprocessedFiles, $fileObj);
        }

        try {
            DB::beginTransaction();

            $returnArray = array();
            foreach ($unprocessedFiles as $file) {
                $originName = $file->getClientOriginalName();
                $mimeType = $file->getClientMimeType();
                $sha1Name = sha1_file($file);
                $folderName = substr($sha1Name, 0, 2);
                $savedPath = $folderName.'/'.$sha1Name;

                $attach = null;
                if (Storage::disk('local')->has($savedPath)) {
                    Log::info("File $originName already existed");

                    $attach = Attachment::where('path', '=', $savedPath)->first();
                }

                if ($attach == null) {
                    //-- virus scan first
                    $virusStatus = self::VIRUSTOTAL_RESULT_NOT_SCANNED;
                    if (config('services.virustotal.apikey')) {
                        $virusScanResult = json_decode(file_get_contents('https://www.virustotal.com/vtapi/v2/file/report?apikey='.config('services.virustotal.apikey').'&resource='.$sha1Name));
                        if ($virusScanResult != NULL) {
                            if ($virusScanResult->response_code == 1) {
                                if ($virusScanResult->positives > 0) {
                                    $virusStatus = self::VIRUSTOTAL_RESULT_POSITIVES;
                                }
                                else {
                                    $virusStatus = self::VIRUSTOTAL_RESULT_NEGATIVES;
                                }
                            }
                        }
                        Log::info("Virustotal scanned status: $virusStatus");
                    }

                    Storage::put($savedPath, file_get_contents($file));
                    unlink($file);
                    Log::info("File $originName has been moved to $savedPath");

                    $attach = new Attachment();
                    $attach->name = $originName;
                    $attach->path = $savedPath;
                    $attach->type = $mimeType;
                    $attach->sha1 = $sha1Name;
                    $attach->status = $virusStatus;
                    $attach->save();
                }

                $id = $attach->id;
                array_push($returnArray, [
                    "id" => $id,
                    "name" => $attach->name,
                    "mime" => $attach->type,
                    "asset_path" => asset("/file/$id"),
                    "download_path" => asset("/file/$id/download")
                ]);
            }

            DB::commit();

            return response()->json($returnArray, 200);
        }
        catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json("Error uploading file", 400);
        }
        finally {
            if (File::isDirectory($extractTargetPath)) {
                File::deleteDirectory($extractTargetPath);
            }
        }
    }

    public function access($ath_id)
    {
        try
        {
            $attach = Attachment::findOrFail($ath_id);

            if(!Storage::disk('local')->has($attach->path))
            {
                throw new Exception("File Not Exists in path: $attach->path");
            }
        }
        catch(Exception $e)
        {
            $errorMsg = $e->getMessage();
            Log::error("AttachmentController::access() : File id $ath_id not exist, $errorMsg");

            return response()->make("", 404);
        }

        $file_name = $attach->name;
        $mime_type = $attach->type;
        $file_stream = Storage::getDriver()->readStream($attach->path);

        $response = response()->stream(function() use ($file_stream) {
                while(@ob_end_clean());
                fpassthru($file_stream);
            },
            200,
            array(
                'Content-Type' => $mime_type,
                "Cache-Control" => " private, max-age=86400",
            )
        );

        return $response;
    }

    public function download($ath_id)
    {
        try
        {
            $attach = Attachment::findOrFail($ath_id);

            if(!Storage::disk('local')->has($attach->path))
            {
                throw new Exception("File Not Exists in path: $attach->path");
            }
        }
        catch(Exception $e)
        {
            $errorMsg = $e->getMessage();
            Log::error("AttachmentController::download() : File id $ath_id not exist, $errorMsg");

            abort(404);
        }

        $file_name = $attach->name;
        $mime_type = $attach->type;
        $file_stream = Storage::getDriver()->readStream($attach->path);

        $response = response()->stream(function() use ($file_stream) {
                while(@ob_end_clean());
                fpassthru($file_stream);
            },
            200,
            array(
                'Content-Type' => $mime_type,
                'Content-Disposition' => "attachment; filename=$file_name"
            )
        );

        return $response;
    }

    public function get_attach_data($ath_id)
    {
        $attach_data = Attachment::select([
                'id',
                'name',
                'type'
            ])->find($ath_id);

        return response($attach_data, 200);
    }

    private function getResponseArray($attachOrm)
    {
        $id = $attachOrm->id;
        return array(
            "id" => $id,
            "name" => $attachOrm->name,
            "mime" => $attachOrm->type,
            "asset_path" => asset("/file/$id"),
            "download_path" => asset("/file/$id/download")
        );
    }

    /**
     *   @return \Symfony\Component\HttpFoundation\File\UploadedFile object
     *   @param string $path file's path
     */
    private function getFileObjectFromPath($path) {
        $originName = File::name($path).'.'.File::extension($path);
        $mimeType = File::mimeType($path);
        $size = File::size($path);

        return new UploadedFile(
            $path,
            $originName,
            $mimeType,
            $size,
            null,
            false
        );
    }
}
