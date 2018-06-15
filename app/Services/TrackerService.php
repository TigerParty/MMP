<?php

namespace App\Services;


class TrackerService
{
    /**
     * tracker Id
     * @var int
     */
    private $entityId;

    /**
     * tracker
     * @var string
     */
    private $entityType;

    /**
     * Tracker Path [[lat, lng, unixtime, alt], [lat, lng, unixtime, alt], ex...]
     * @var array
     */
    private $inputPath;

    /**
     * Result of path meta to be generated
     * @var json
     */
    private $result;

    public function __construct($entityId, $entityType)
    {

        if ($entityType != 'tracker') {
            throw new \Exception("Invalid entity type: $entityType");
        }

        $this->entityId = $entityId;
        $this->entityType = $entityType;
    }

    public function calcTrackerMeta($inputPath)
    {
        $dateTimeFormat = "Y-m-d H:i:s";
        $result = array(
            'avg_speed' => 0,
            'start_at' => "",
            'end_at' => "",
        );
        try {
            if (!$inputPath) {
                \Log::warning('Got empty tracker path data');
                $this->result = $result;
                return $this;
            }

            // format: unixtime
            $startTime = $inputPath[0][2];
            $endTime = end($inputPath)[2];

            // -- TotalDistance
            $totalDistance = 0;
            foreach ($inputPath as $index => $coordinate) {
                $distance = 0;
                if ($index != 0) {
                    $distance = sqrt(
                        pow($inputPath[$index][0] - $inputPath[$index - 1][0], 2) +
                        pow($inputPath[$index][1] - $inputPath[$index - 1][1], 2)
                    );
                }
                $totalDistance += $distance;
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
        } catch (Exception $e) {
            \Log::error($e);
            throw $e;
        }

        $this->result = $result;
        return $this;
    }

    public function getResult()
    {
        return $this->result;
    }
}
