<?php

use Illuminate\Database\Seeder;

class TestCitizenSmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Argo\CitizenSMS::class, 20)
            ->create()
            ->each(function ($citizenSms) {
                factory(\App\Argo\CitizenSMSReply::class, 3)->create([
                    'citizen_sms_id' => $citizenSms->id
                ]);
            });
    }
}
