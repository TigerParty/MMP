<?php

namespace App\Repositories;

use DB;

class AttachmentRepository {
    public function getSliderOnMonths($attachableId, $formIds, $attachableType)
    {
        $rows = DB::table('attachables AS rel')
            ->join('attachment AS ath', function($join) use ($attachableType) {
                $join->on('ath.id', '=', 'rel.attachment_id')
                    ->on('rel.attachable_type', '=',
                        DB::raw(app('db')->getPdo()->quote($attachableType))
                    );
            })
            ->where('rel.attachable_id', '=', $attachableId)
            ->where('rel.attachable_type', '=', DB::raw(app('db')->getPdo()->quote($attachableType)))
            ->whereIn('rel.attached_form_id', $formIds)
            ->whereNotNull('rel.attached_at')
            ->where(function($query){
                $query->where('ath.type', 'LIKE', 'image/%')
                    ->orWhere('ath.type', 'LIKE', 'video/%');
            })
            ->orderBy('rel.attached_at', 'DESC')
            ->get([
                "ath.id AS attachment_id",
                "ath.name AS attachment_name",
                "ath.type AS attachment_type",
                DB::raw("DATE_FORMAT(rel.attached_at, '%Y-%m') AS attached_at_month"),
                "rel.id AS relation_id",
                "rel.attached_form_id AS attached_form_id",
                "rel.attached_at AS attached_at",
                "rel.description AS description",
            ]);

        $result = array();
        foreach($rows as $row)
        {
            $monthKey = date('F, Y', strtotime($row->attached_at_month));

            if(!array_key_exists($row->attached_form_id, $result))
            {
                $result[$row->attached_form_id] = array();
            }

            if(!array_key_exists($monthKey, $result[$row->attached_form_id]))
            {
                $result[$row->attached_form_id][$monthKey] = array(
                    "attached_at" => $monthKey,
                    "items" => array()
                );
            }

            array_push($result[$row->attached_form_id][$monthKey]['items'], array(
                'relation_id' => $row->relation_id,
                'attachment_id' => $row->attachment_id,
                'attachment_path' => argo_image_path($row->attachment_id),
                'attachment_type' => $row->attachment_type,
                'attached_at' => $row->attached_at,
                'description' => json_decode($row->description)
            ));
        }

        return $result;
    }

    public function getPageAttachments($attachableId, $attachableType)
    {
        $rows = DB::table('attachables')
            ->leftJoin('attachment', function($join) use ($attachableType){
                $join->on('attachables.attachment_id', '=', 'attachment.id')
                    ->on('attachables.attachable_type', '=', DB::raw(app('db')->getPdo()->quote($attachableType)));
            })
            ->whereNull('attachables.attached_form_id')
            ->where('attachables.attachable_id', '=', $attachableId)
            ->where('attachables.attachable_type', '=', DB::raw(app('db')->getPdo()->quote($attachableType)))
            ->get([
                'attachables.attachment_id',
                'attachment.name AS attachment_name',
                'attachment.type AS attachment_type',
                'attachment.status AS attachment_status',
                'attachables.attached_at'
            ]);

        $result = array();
        foreach($rows as $row)
        {
            array_push($result, array(
                'id' => $row->attachment_id,
                'path' => argo_image_path($row->attachment_id),
                'name' => $row->attachment_name,
                'type' => $row->attachment_type,
                'attached_at' => date(DATE_ISO8601, strtotime($row->attached_at)),
                'status' => $row->attachment_status
            ));
        }
        return $result;

    }
}
