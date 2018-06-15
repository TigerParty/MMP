<?php

namespace App\Http\Controllers\Feedback;


use App\Argo\ReportCitizen;
use App\Http\Controllers\Controller;

class CitizenReportController extends Controller
{
    const APP_ITEMS_PER_PAGE = 10;

    function reportIndexApi()
    {
        $ormReportCitizen = ReportCitizen::select([
            'id', 'comment', 'created_at', 'is_read'
        ])->orderBy('created_at', 'DESC');

        //-- new amount
        $ormNewAmountReportCitizen = clone $ormReportCitizen;
        $newAmount = $ormNewAmountReportCitizen->where('is_read', 0)->count();

        //-- paginate
        $paginated = $ormReportCitizen->paginate(self::APP_ITEMS_PER_PAGE);

        $data = $paginated->items();

        return response()->json([
            'is_admin' => argo_is_admin_accessible(),
            'new_amount' => $newAmount,
            'total_amount' => $paginated->total(),
            'report' => $data,
            'per_page' => $paginated->perPage(),
        ]);
    }

    function reportShowApi($id)
    {
        $data = ReportCitizen::select([
            'id', 'comment', 'lat', 'lng', 'meta', 'created_at', 'is_read'
        ])->with([
            'attachments' => function ($query) {
                $query->select([
                    'attachment.id'
                ])->withPivot(['description']);
            }
        ])->findOrFail($id);

        return response()->json($data);
    }

    function reportDeleteApi($id)
    {
        try {
            ReportCitizen::findOrFail($id)->delete();

            return response()->json(true);
        } catch (\Exception $e) {
            return response()->json("Delete failed", 400);
        }
    }

    function reportMarkReadApi($id)
    {
        try {
            ReportCitizen::findOrFail($id)
                ->where('is_read', false)
                ->update([
                    'is_read' => true
                ]);

            return response()->json(true);
        } catch (\Exception $e) {
            return response()->json("Mark read for app report failed", 400);
        }
    }
}
