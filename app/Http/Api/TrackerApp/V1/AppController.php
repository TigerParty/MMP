<?php

namespace App\Http\Api\TrackerApp\V1;

use App\Http\Controllers\AttachmentController;
use Carbon\Carbon;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

use App\Argo\User;
use App\Argo\Tracker;


class AppController extends BaseController
{
    public function download()
    {
        $users = User::selectRaw('
            user.id,
            user.name,
            user.password,
            permission_level.name AS perm,
            permission_level.priority AS perm_level
        ')
        ->leftJoin('permission_level', 'user.permission_level_id', '=', 'permission_level.id')
        ->where('permission_level.priority', '!=', 1)
        ->get()
        ->makeVisible([
            'password'
        ]);

        return response()->json([
            'user' => $users,
        ], 200);
    }

    public function submit(Request $request)
    {
        $trackerOrm = new Tracker();

        $trackerOrm->title = $request->get('title', null);
        $trackerOrm->description = $request->get('description', null);
        $trackerOrm->lat = $request->get('lat', null);
        $trackerOrm->lng = $request->get('lng', null);
        $trackerOrm->source = 'http:v1';
        $trackerOrm->path = $request->get('path', null);
        $trackerOrm->created_by = $request->get('created_by', null);
        $trackerOrm->save();

        $trackerOrm->meta = $this->getTrackerMeta($request->get('path', null));
        $trackerOrm->save();

        //-- attachments
        $attaches = $request->get('attaches', []);
        $attachData = [];
        if (count($attaches)) {
            foreach ($attaches as $attach) {
                $attachId = array_get($attach, 'id', null);
                $attachedAt = array_get($attach, 'attached_at', Carbon::now()->timestamp);
                $attachDescription = array_get($attach, 'description', []);

                if ($attachId) {
                    $attachData[$attachId] = [
                        'attached_at' => Carbon::createFromTimestampUTC($attachedAt),
                        'description' => json_encode($attachDescription)
                    ];
                }
            }

            $trackerOrm->attaches()->sync($attachData);
        }

        return response()->json([
            'status' => 'ok'
        ], 200);
    }

    function uploadFile(Request $request)
    {
        $controller = new AttachmentController();

        if ($request->hasFile('file'))
        {
            $file = $request->file('file');
            return $controller->doUpload($file);
        }

        return response()->json([
            "status" => "file not attached"
        ], 400);
    }

    private function getTrackerMeta($paths)
    {
        if (!$paths || !is_array($paths) || empty($paths)) {
            \Log::warning('Got empty tracker path data');
            return array(
                'avg_speed' => 0,
                'start_at' => '',
                'end_at' => '',
            );
        }

        $dateTimeFormat = "Y-m-d H:i:s";

        try {
            // format: unixtime
            $startTime = array_first(array_first($paths))[2];
            $endTime = array_last(array_last($paths))[2];

            //-- TotalDistance
            $totalDistance = 0;
            foreach ($paths as $path) {
                foreach ($path as $index => $coordinate) {
                    $distance = 0;
                    if ($index != 0) {
                        $distance = sqrt(
                            pow($path[$index][0] - $path[$index - 1][0], 2) +
                            pow($path[$index][1] - $path[$index - 1][1], 2)
                        );
                    }
                    $totalDistance += $distance;
                }
            }

            // -- AvgSpeed
            $avgSpeed = 0;
            $timeLong = ($endTime - $startTime) / 3600;
            if ($timeLong > 0) {
                $avgSpeed = round($totalDistance * 111 / $timeLong, 2);
            }

            $result = array(
                'avg_speed' => $avgSpeed,
                'start_at' => date($dateTimeFormat, $startTime),
                'end_at' => date($dateTimeFormat, $endTime),
            );

            info("Tracker result", $result);

            return $result;
        } catch (Exception $e) {
            \Log::error($e);
            throw $e;
        }
    }
}
