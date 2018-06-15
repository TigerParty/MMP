<?php

use App\Argo\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    public function run()
    {
        $records = array(
            array('id' => 1, 'name' => "development", 'password' => Hash::make('development'), 'permission_level_id' => 1, 'email' => "development@thetigerparty.com"),
            array('id' => 2, 'name' => "TP_admin", 'password' => Hash::make('TP_admin'), 'permission_level_id' => 2, 'email' => "development@thetigerparty.com"),
            array('id' => 3, 'name' => "TP_regular_user", 'password' => Hash::make('TP_regular_user'), 'permission_level_id' => 3, 'email' => "development@thetigerparty.com"),
            array('id' => 4, 'name' => "TP_super_admin", 'password' => Hash::make('TP_super_admin'), 'permission_level_id' => 2, 'email' => "development@thetigerparty.com"),
            array('id' => 5, 'name' => "regular", 'password' => Hash::make('regular'), 'permission_level_id' => 3, 'email' => "development@thetigerparty.com"),
            array('id' => 6, 'name' => "coordinator", 'password' => Hash::make('coordinator'), 'permission_level_id' => 3, 'email' => "development@thetigerparty.com"),
        );
        $this->command->info("User : Seeding " . count($records) . " records...");
        foreach ($records as $record) {
            User::create($record);
        }
    }
}
