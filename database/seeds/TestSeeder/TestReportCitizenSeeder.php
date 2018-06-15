<?php

use Illuminate\Database\Seeder;

class TestReportCitizenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Argo\ReportCitizen::class, 30)->create()
            ->each(function ($report) {
                $report->attachments()->attach([1, 2, 3]);
            });
    }
}
