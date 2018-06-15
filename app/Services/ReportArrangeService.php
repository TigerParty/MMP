<?php

namespace App\Services;

use App\Argo\Category;
use App\Argo\ReportRaw;
use DB;
use Log;

class ReportArrangeService
{
    public static function saveRawReport($raw_data, $source)
    {
        try {
            DB::beginTransaction();
            $report_raw_orm = new ReportRaw();
            $report_raw_orm->payload = json_encode($raw_data);
            $report_raw_orm->source = $source;
            $report_raw_orm->save();
            DB::commit();
            return $report_raw_orm;
        } catch (Exception $e) {
            DB::rollback();
            Log::error('ReportArrangeService::saveRawReport() : ' . $e);
        }
    }

    public static function parseGPSData($gps_data)
    {
        try {
            $gps_data = json_decode($gps_data, true);

            if (!$gps_data) {
                return NULL;
            }

            $start_at = date("Y-m-d H:i:s", $gps_data[0][2]);
            $end_at = date("Y-m-d H:i:s", end($gps_data)[2]);

            $total_distance = 0;

            foreach ($gps_data as $key => $value) {
                $distance = 0;

                if ($key != 0) {
                    $distance = sqrt(pow($gps_data[$key][0] - $gps_data[$key - 1][0], 2) +
                        pow($gps_data[$key][1] - $gps_data[$key - 1][1], 2)
                    );

                    $total_distance += $distance;
                }
            }

            $avg_speed = 0;
            $hour_time = (end($gps_data)[2] - $gps_data[0][2]) / 3600;

            if ($hour_time != 0.0) {
                $avg_speed = $total_distance * 111 / $hour_time; //--km/hr
                $avg_speed = round($avg_speed, 2);
            }

            $data = array(
                'avg_speed' => $avg_speed,
                'start_at' => $start_at,
                'end_at' => $end_at,
                'coordinates' => $gps_data
            );

            return json_encode($data, JSON_NUMERIC_CHECK);
        } catch (Exception $e) {
            Log::error("ReportArrangeService::parseGPSData() : " . $e->getMessage());
            Log::error($e);

            $data = array(
                'avg_speed' => 0,
                'start_at' => "",
                'end_at' => "",
                'coordinates' => $gps_data
            );

            return json_encode($data, JSON_NUMERIC_CHECK);
        }
    }
}
