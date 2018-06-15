<?php

namespace App\Repositories;

use App\Argo\Report;
use DB;
use Log;

class ReportRepository
{
    public function basicInfo()
    {
        return Report::select([
            'report.id',
            'report.description',
            'report.lat',
            'report.lng',
            'report.version',
            'report.region_ids',
            'report.updated_at',
            'project.id AS project_id',
            'project.title AS project_title',
            'plv.name AS view_level',
            'ple.name AS edit_level',
            'dynamic_form.name AS dynamic_form_name',
            'user_c.name AS created_by',
            'user_u.name AS updated_by'
        ])
            ->leftJoin('user AS user_c', 'report.created_by', '=', 'user_c.id')
            ->leftJoin('user AS user_u', 'report.updated_by', '=', 'user_u.id')
            ->leftJoin('project', 'project.id', '=', 'report.project_id')
            ->leftJoin('permission_level AS plv', 'plv.id', '=', 'report.view_level_id')
            ->leftJoin('permission_level AS ple', 'ple.id', '=', 'report.edit_level_id')
            ->leftJoin('dynamic_form', 'report.form_id', '=', 'dynamic_form.id');
    }

    public function getLastedImagesByIds($report_ids)
    {
        $images = DB::table('attachables AS atb')
            ->select(DB::raw('
                atb.attachable_id AS rp_id,
                max(att.id) AS att_id
            '))
            ->leftjoin('attachment AS att', 'atb.attachment_id', '=', 'att.id')
            ->where('atb.attachable_type', '=', 'App\\Argo\\Report')
            ->where('att.type', 'LIKE', 'image/%')
            ->whereIn('atb.attachable_id', $report_ids)
            ->groupBy('atb.attachable_id')
            ->get();

        return $images;
    }

    public function getLastedImageById($id)
    {
        $image = DB::table('attachables AS atb')
            ->select(DB::raw('
                atb.attachable_id AS rp_id,
                max(att.id) AS att_id
            '))
            ->leftjoin('attachment AS att', 'atb.attachment_id', '=', 'att.id')
            ->where('atb.attachable_type', '=', 'App\\Argo\\Report')
            ->where('att.type', 'LIKE', 'image/%')
            ->where('atb.attachable_id', '=', $id)
            ->first();

        return $image;
    }
}
