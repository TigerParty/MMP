<?php

use App\Argo\Survey;
use Illuminate\Database\Seeder;

class TestSurveySeeder extends Seeder
{
    public function run()
    {
        $records = array(
            array(
                'source' => 'argopbx',
                'source_id' => 801,
                'status' => 'new',
                'payload' => [
                    "data"=> [
                        "questions"=> [
                            [
                                "title"=> "Please update the issue",
                                "response_type"=> 3,
                                "response"=> [
                                    "open_audio_url"=> ""
                                ]
                            ]
                        ]
                    ],
                    "base"=> [
                        "phone"=> "801",
                        "start_timestamp"=> "2017-09-13 00:00:00"
                    ]
                ]
            ),
            array(
                'source' => 'argopbx',
                'source_id' => 802,
                'status' => 'new',
                'payload' => [
                    "data"=> [
                        "questions"=> [
                            [
                                "title"=> "Please update the issue",
                                "response_type"=> 3,
                                "response"=> [
                                    "open_audio_url"=> ""
                                ]
                            ]
                        ]
                    ],
                    "base"=> [
                        "phone"=> "802",
                        "start_timestamp"=> "2017-09-13 01:00:00"
                    ]
                ]
            ),
            array(
                'source' => 'argopbx',
                'source_id' => 803,
                'status' => 'new',
                'payload' => [
                    "data"=> [
                        "questions"=> [
                            [
                                "title"=> "Please update the issue",
                                "response_type"=> 3,
                                "response"=> [
                                    "open_audio_url"=> ""
                                ]
                            ]
                        ]
                    ],
                    "base"=> [
                        "phone"=> "803",
                        "start_timestamp"=> "2017-09-13 02:00:00"
                    ]
                ]
            ),
        );
        $this->command->info("Survey : Seeding " . count($records) . " records...");
        foreach ($records as $record) {
            Survey::create($record);
        }
    }
}
