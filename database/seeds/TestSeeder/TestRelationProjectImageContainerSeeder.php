<?php
use Illuminate\Database\Seeder;

class TestRelationProjectImageContainerSeeder extends Seeder
{
    public function run()
    {
        $records = array(
            array('project_id' => 1, 'container_id' => 3, 'cover_image_id' => 36),
            array('project_id' => 1, 'container_id' => 4, 'cover_image_id' => 39),
            array('project_id' => 1, 'container_id' => 5, 'cover_image_id' => 35),
            array('project_id' => 1, 'container_id' => 6, 'cover_image_id' => 37),
            array('project_id' => 1, 'container_id' => 7, 'cover_image_id' => 34),
            array('project_id' => 1, 'container_id' => 8, 'cover_image_id' => 38),
            array('project_id' => 1, 'container_id' => 9, 'cover_image_id' => 33),
        );
        $this->command->info("TestRelationProjectImageContainer : Seeding " . count($records) . " records...");
        foreach($records as $record) {
            DB::table('relation_project_image_container')->insert($record);
        }
    }
}
