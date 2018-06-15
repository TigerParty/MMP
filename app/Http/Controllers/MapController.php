<?php
namespace App\Http\Controllers;

use DB;

use App\Argo\ReporterLocation;
use App\Argo\ReportCitizen;
use App\Argo\Tracker;

class MapController extends Controller
{
    public function last_reporter_location()
    {
        $reporter_locations = ReporterLocation::select([
                'reporter_location.device_id',
                'reporter_location.lat',
                'reporter_location.lng',
                'reporter_location.created_at',
                'reporter_location.created_by'
            ])
            ->rightJoin(
                DB::raw(
                    '(SELECT device_id, max(id) AS last_updated_id '.
                    'FROM reporter_location '.
                    'GROUP BY device_id) AS subquery'
                ),
                function($join){
                    $join->on('reporter_location.id', '=', 'subquery.last_updated_id');
                }
            )->get();

        return response()->json($reporter_locations, 200, [], JSON_NUMERIC_CHECK);
    }

    public function citizenReportApi()
    {
        $report = ReportCitizen::select([
                'report_citizen.id',
                'report_citizen.comment',
                'report_citizen.email',
                'report_citizen.lat',
                'report_citizen.lng',
                'report_citizen.created_at',
                DB::raw('MAX(attachables.attachment_id) AS attachment_id')
            ])
            ->leftJoin('attachables', function($join){
                $join->on('attachable_type', '=', DB::raw("'App\\\\Argodf\\\\ReportCitizen'"))
                ->on('attachable_id', '=', DB::raw("report_citizen.id"));
            })
            ->groupBy('report_citizen.id')
            ->get();

        return response()->json($report, 200, [], JSON_NUMERIC_CHECK);
    }

    public function trackerApi()
    {
        $allTracker = Tracker::with(
            array(
                'attaches' => function ($query) {
                    $query
                    ->select(array('attachment.id', 'created_at'))
                    ->withPivot('description');
                }
            )
        )->get(array('id', 'title', 'path', 'created_at', 'meta'));

        // -- JSON decode description
        $allTracker->each(function ($tracker) {
            $tracker->attaches->each(function ($attach) {
                $attach->pivot->description = json_decode($attach->pivot->description);
            });
        });

        return response($allTracker, 200);
    }
}
