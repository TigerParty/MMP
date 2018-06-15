<?php

use App\Argo\Aggregation;
use Illuminate\Database\Seeder;

class TestAggregationSeeder extends Seeder
{
    public function run()
    {
        $records = array(
            array(
                'id' => 1,
                'title' => 'Student Avg Weight',
                'type' => 'avg',
                'container_id' => 1,
                'target_container_id' => 8,
                'target_field_id' => 47,
                'filters' => [
                    [
                        "field_id" => 11,
                        "operator" => "=",
                        "value" => "Option1"
                    ]
                ],
                'order' => 1
            ),
            array('id' => 2,
                'title' => 'Student Count',
                'type' => 'count',
                'container_id' => 1,
                'target_container_id' => 8,
                'target_field_id' => null,
                'filters' => [],
                'order' => 2
            ),
            array('id' => 3,
                'title' => 'Student Major Subject Ratio',
                'type' => 'ratio',
                'container_id' => 1,
                'target_container_id' => 8,
                'target_field_id' => 11,
                'filters' => [],
                'order' => 3
            ),
        );
        $this->command->info("Aggregation : Seeding " . count($records) . " records...");
        foreach ($records as $record) {
            Aggregation::create($record);
        }
    }
}
