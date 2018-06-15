<?php
use Illuminate\Database\Seeder;

class PermissionLevelSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = array(
            array(
                "name" => "System",
                "priority" => 1,
                "color" => "#ff0000",
            ),
            array(
                "name" => "Admin",
                "priority" => 2,
                "color" => "#ff7965",
            ),
            array(
                "name" => "Coordinator",
                "priority" => 3,
                "color" => "#f3cc00",
            ),
            array(
                "name" => "Reporter",
                "priority" => 4,
                "color" => "#4fbf2b",
            ),
            array(
                "name" => "Public",
                "priority" => 5,
                "color" => "#4880ff",
            )
        );

        $this->command->info("Permission Level : Migrating " . count($records) . " Permission Levels...");
        foreach ($records as $record) {
            DB::table('permission_level')->insert($record);
        }
    }
}
