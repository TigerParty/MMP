<?php
use Illuminate\Database\Seeder;


class TestReportValueSeeder extends Seeder
{
    public function run()
    {
        $records = array(
            array('report_id' => 1, 'form_field_id' => 4, 'value' => rand(30,100)),
            array('report_id' => 2, 'form_field_id' => 4, 'value' => rand(30,100)),
            array('report_id' => 3, 'form_field_id' => 4, 'value' => rand(30,100)),
            array('report_id' => 4, 'form_field_id' => 4, 'value' => rand(30,100)),
            array('report_id' => 5, 'form_field_id' => 4, 'value' => rand(30,100)),
            array('report_id' => 6, 'form_field_id' => 4, 'value' => rand(30,100)),
            array('report_id' => 7, 'form_field_id' => 4, 'value' => rand(30,100)),
            array('report_id' => 8, 'form_field_id' => 4, 'value' => rand(30,100)),
            array('report_id' => 9, 'form_field_id' => 4, 'value' => rand(30,100)),
            array('report_id' => 10, 'form_field_id' => 4, 'value' => rand(30,100)),
            array('report_id' => 7, 'form_field_id' => 5, 'value' => rand(10,30)),
            array('report_id' => 8, 'form_field_id' => 5, 'value' => rand(10,30)),
            array('report_id' => 9, 'form_field_id' => 5, 'value' => rand(10,30)),
            array('report_id' => 10, 'form_field_id' => 5, 'value' => rand(10,30)),
            array('report_id' => 11, 'form_field_id' => 5, 'value' => rand(10,30)),
            array('report_id' => 12, 'form_field_id' => 5, 'value' => rand(10,30)),
            array('report_id' => 13, 'form_field_id' => 5, 'value' => rand(10,30)),
            array('report_id' => 14, 'form_field_id' => 5, 'value' => rand(10,30)),
            array('report_id' => 15, 'form_field_id' => 5, 'value' => rand(10,30)),
            array('report_id' => 16, 'form_field_id' => 5, 'value' => rand(10,30)),
        );
        $this->command->info("Report value: Seeding " . count($records) . " records...");

        DB::table('report_value')->insert($records);
    }
}
