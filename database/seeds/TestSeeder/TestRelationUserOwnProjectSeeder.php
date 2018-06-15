<?php

use Illuminate\Database\Seeder;

class TestRelationUserOwnProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = array(
            array('user_id' => 3, 'project_id' => 1,),
            array('user_id' => 3, 'project_id' => 2,),
            array('user_id' => 3, 'project_id' => 10,),
            array('user_id' => 3, 'project_id' => 11,),
            array('user_id' => 3, 'project_id' => 15,),
            array('user_id' => 3, 'project_id' => 16,),
            array('user_id' => 3, 'project_id' => 7,),

            array('user_id' => 5, 'project_id' => 3,),
            array('user_id' => 5, 'project_id' => 4,),
            array('user_id' => 5, 'project_id' => 12,),
            array('user_id' => 5, 'project_id' => 13,),
            array('user_id' => 5, 'project_id' => 17,),
            array('user_id' => 5, 'project_id' => 18,),
            array('user_id' => 5, 'project_id' => 8,),

            array('user_id' => 6, 'project_id' => 5,),
            array('user_id' => 6, 'project_id' => 6,),
            array('user_id' => 6, 'project_id' => 14,),
            array('user_id' => 6, 'project_id' => 19,),
            array('user_id' => 6, 'project_id' => 20,),
            array('user_id' => 6, 'project_id' => 9,),
        );
        $this->command->info("TestRelationUserOwnProjectSeeder : Seeding " . count($records) . " records...");
        foreach ($records as $record) {
            DB::table('relation_user_own_project')->insert($record);
        }
    }
}
