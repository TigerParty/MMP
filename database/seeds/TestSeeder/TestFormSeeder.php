<?php
use Illuminate\Database\Seeder;
use App\Argo\DynamicForm;

class TestFormSeeder extends Seeder {
    public function run() {
        $records = array(
            array('id' => 1, 'name' => 'School Basic Info'),
            array('id' => 2, 'name' => 'Road Basic Info'),

            array('id' => 3, 'name' => 'Student Basic Info'),
            array('id' => 4, 'name' => 'Faculty Info'),

            array('id' => 5, 'name' => 'Subsidy Info'),
            array('id' => 6, 'name' => 'Scholarshiop Info'),
            array('id' => 7, 'name' => 'SPPP Info'),
            array('id' => 8, 'name' => 'Infrastructure Info'),

            array('id' => 9, 'name' => 'Construction Progress Info'),
            array('id' => 10, 'name' => 'Construction Monitoring Info'),

            array('id' => 11, 'name' => 'Speed Tracker'),
            array('id' => 12, 'name' => 'IRI Tracker'),

            array('id' => 13, 'name' => 'Unit Test'),
        );
        $this->command->info("DynamicForm : Seeding " . count($records) . " records...");
        foreach($records as $record) {
            DB::table('dynamic_form')->insert($record);
        }
    }
}
