<?php
use Illuminate\Database\Seeder;

class TestRelationFieldFilterContainer extends Seeder {
    public function run() {
        $records = array(
            array('form_field_id' => 2, 'container_id' => 1),
            array('form_field_id' => 4, 'container_id' => 1),
            array('form_field_id' => 5, 'container_id' => 1),
            array('form_field_id' => 7, 'container_id' => 2),
            array('form_field_id' => 9, 'container_id' => 8),
            array('form_field_id' => 11, 'container_id' => 8),
        );

        $this->command->info("RelationFieldFilterContainer : Seeding " . count($records) . " records...");
        foreach($records as $record) {
            DB::table('relation_field_filter_container')->insert($record);
        }
    }
}
