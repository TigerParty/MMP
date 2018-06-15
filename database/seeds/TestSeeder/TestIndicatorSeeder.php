<?php
use Illuminate\Database\Seeder;

class TestIndicatorSeeder extends Seeder
{
    public function run()
    {
        $records = array(
            array('id' => 1,
                'title' => '{"text":"No. of Teachers History"}',
                'options' => '{"chart":{"type":"line"}}',
                'yaxis' => '{"title":{"text":"No. of Teachers"}}',
                'rule' => 'year',
                'xaxis_limit' => 8,
                'data_fields' => '[4]',
                'indicate_id' => 1,
                'indicate_type' => 'App\\Argo\\Container'),
            array('id' => 2,
                'title' => '{"text":"No. of Trainned Teachers History"}',
                'options' => '{"chart":{"type":"line"}}',
                'yaxis' => '{"title":{"text":"No. of Trainned Teachers"}}',
                'rule' => 'month',
                'xaxis_limit' => 6,
                'data_fields' => '[5]',
                'indicate_id' => 1,
                'indicate_type' => 'App\\Argo\\Container'),
            array('id' => 3,
                'title' => '{"text":"Student Weight"}',
                'options' => '{"chart":{"type":"column"}}',
                'yaxis' => '{"title":{"text":"Student Weight"}}',
                'rule' => 'subproject',
                'xaxis_limit' => 10,
                'data_fields' => '[47]',
                'indicate_id' => 1,
                'indicate_type' => 'App\\Argo\\Container'),
        );

        $this->command->info("Indicator : Seeding " . count($records) . " records...");

        DB::table('indicator')->insert($records);
    }
}
