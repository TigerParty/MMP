<?php
use Illuminate\Database\Seeder;

class TestContainerSeeder extends Seeder
{
    public function run()
    {
        $records = array(
            array(
                'id' => 1,
                'parent_id' => null,
                'name' => "School",
                'uid_rule' => '{"unique_on":{"project":["container_id"]}}',
                'form_id' => 1,
                'title_duplicatable' => false
            ),
            array(
                'id' => 2,
                'parent_id' => null,
                'name' => "Roads",
                'form_id' => 2,
                'title_duplicatable' => false
            ),
            array(
                'id' => 3,
                'parent_id' => 1,
                'name' => "School Management Tools",
                'form_id' => null,
                'title_duplicatable' => false
            ),
            array(
                'id' => 4,
                'parent_id' => 3,
                'name' => "School Subsidy",
                'form_id' => 5,
                'title_duplicatable' => false
            ),
            array(
                'id' => 5,
                'parent_id' => 3,
                'name' => "Scholarship",
                'form_id' => 6,
                'title_duplicatable' => false
            ),
            array(
                'id' => 6,
                'parent_id' => 3,
                'name' => "SPPP",
                'form_id' => 7,
                'title_duplicatable' => false
            ),
            array(
                'id' => 7,
                'parent_id' => 3,
                'name' => "School Infrastructure",
                'form_id' => 8,
                'title_duplicatable' => false
            ),
            array(
                'id' => 8,
                'parent_id' => 1,
                'name' => "Student",
                'form_id' => 3,
                'uid_rule' => '{"unique_on":{"project":["container_id","parent_id"]}}',
                'card_rule' => '{"fields":[51,48,49,50]}',
                'title_duplicatable' => false
            ),
            array(
                'id' => 9,
                'parent_id' => 1,
                'name' => "Faculty",
                'form_id' => 4,
                'title_duplicatable' => false
            ),
            array(
                'id' => 10,
                'parent_id' => 2,
                'name' => "Road Construction",
                'form_id' => 9,
                'title_duplicatable' => false
            ),
            array(
                'id' => 11,
                'parent_id' => 2,
                'name' => "Road Maintenance",
                'form_id' => 10,
                'title_duplicatable' => false
            ),
        );

        $this->command->info("Container : Seeding " . count($records) . " records...");
        foreach ($records as $record) {
            DB::table('container')->insert($record);
        }
    }
}
