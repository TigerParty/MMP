<?php
use Illuminate\Database\Seeder;

class TestAttachablesSeeder extends Seeder {
    public function run() {
        $records = array(
            array('attachment_id' => 2, 'attachable_id' => 1, 'attachable_type' => "App\Argo\Project", 'attached_at' => '2016-06-01 08:00:00', 'attached_form_id' => 1, 'description' => json_encode([
                'header' => 'Dubai',
                'content' => 'Sample time lapse slider'
            ])),
            array('attachment_id' => 3, 'attachable_id' => 1, 'attachable_type' => "App\Argo\Project", 'attached_at' => '2016-06-01 08:30:00', 'attached_form_id' => 1, 'description' => json_encode([
                'header' => 'Dubai',
                'content' => 'Sample time lapse slider'
            ])),
            array('attachment_id' => 4, 'attachable_id' => 1, 'attachable_type' => "App\Argo\Project", 'attached_at' => '2016-07-20 10:00:00', 'attached_form_id' => NULL, 'description' => json_encode([
                'header' => 'Dubai',
                'content' => 'Sample time lapse slider'
            ])),
            array('attachment_id' => 1, 'attachable_id' => 1, 'attachable_type' => "App\Argo\Project", 'attached_at' => '2016-07-28 10:00:00', 'attached_form_id' => 1, 'description' => json_encode([
                'header' => 'Dubai',
                'content' => 'Sample time lapse slider'
            ])),

            array('attachment_id' => 2, 'attachable_id' => 2, 'attachable_type' => "App\Argo\Project", 'attached_at' => '2016-06-01 08:00:00', 'attached_form_id' => 1, 'description' => json_encode([
                'header' => 'Dubai',
                'content' => 'Sample time lapse slider'
            ])),
            array('attachment_id' => 3, 'attachable_id' => 2, 'attachable_type' => "App\Argo\Project", 'attached_at' => '2016-06-01 08:30:00', 'attached_form_id' => 1, 'description' => json_encode([
                'header' => 'Dubai',
                'content' => 'Sample time lapse slider'
            ])),
            array('attachment_id' => 4, 'attachable_id' => 2, 'attachable_type' => "App\Argo\Project", 'attached_at' => '2016-07-20 10:00:00', 'attached_form_id' => 1, 'description' => json_encode([
                'header' => 'Dubai',
                'content' => 'Sample time lapse slider'
            ])),
            array('attachment_id' => 1, 'attachable_id' => 2, 'attachable_type' => "App\Argo\Project", 'attached_at' => '2016-07-28 10:00:00', 'attached_form_id' => 1, 'description' => json_encode([
                'header' => 'Dubai',
                'content' => 'Sample time lapse slider'
            ])),

            array('attachment_id' => 1, 'attachable_id' => 10, 'attachable_type' => "App\Argo\Project", 'attached_at' => '2016-06-01 08:00:00', 'attached_form_id' => 3, 'description' => json_encode([
                'header' => 'Dubai',
                'content' => 'Sample time lapse slider'
            ])),
            array('attachment_id' => 3, 'attachable_id' => 10, 'attachable_type' => "App\Argo\Project", 'attached_at' => '2016-06-01 08:30:00', 'attached_form_id' => 3, 'description' => json_encode([
                'header' => 'Dubai',
                'content' => 'Sample time lapse slider'
            ])),
            array('attachment_id' => 1, 'attachable_id' => 11, 'attachable_type' => "App\Argo\Project", 'attached_at' => '2016-07-28 10:00:00', 'attached_form_id' => 3, 'description' => json_encode([
                'header' => 'Dubai',
                'content' => 'Sample time lapse slider'
            ])),
            array('attachment_id' => 4, 'attachable_id' => 11, 'attachable_type' => "App\Argo\Project", 'attached_at' => '2016-07-28 10:00:00', 'attached_form_id' => 3, 'description' => json_encode([
                'header' => 'Dubai',
                'content' => 'Sample time lapse slider'
            ])),

            array('attachment_id' => 19, 'attachable_id' => 7, 'attachable_type' => "App\Argo\Project", 'attached_at' => '2003-01-01 00:00:00', 'attached_form_id' => 2, 'description' => json_encode([
                'header' => 'Dubai',
                'content' => 'Sample time lapse slider'
            ])),
            array('attachment_id' => 20, 'attachable_id' => 7, 'attachable_type' => "App\Argo\Project", 'attached_at' => '2004-01-01 00:00:00', 'attached_form_id' => 2, 'description' => json_encode([
                'header' => 'Dubai',
                'content' => 'Sample time lapse slider'
            ])),
            array('attachment_id' => 21, 'attachable_id' => 7, 'attachable_type' => "App\Argo\Project", 'attached_at' => '2005-01-01 00:00:00', 'attached_form_id' => 2, 'description' => json_encode([
                'header' => 'Dubai',
                'content' => 'Sample time lapse slider'
            ])),
            array('attachment_id' => 22, 'attachable_id' => 7, 'attachable_type' => "App\Argo\Project", 'attached_at' => '2006-01-01 00:00:00', 'attached_form_id' => 2, 'description' => json_encode([
                'header' => 'Dubai',
                'content' => 'Sample time lapse slider'
            ])),
            array('attachment_id' => 23, 'attachable_id' => 7, 'attachable_type' => "App\Argo\Project", 'attached_at' => '2007-01-01 00:00:00', 'attached_form_id' => 2, 'description' => json_encode([
                'header' => 'Dubai',
                'content' => 'Sample time lapse slider'
            ])),
            array('attachment_id' => 24, 'attachable_id' => 7, 'attachable_type' => "App\Argo\Project", 'attached_at' => '2008-01-01 00:00:00', 'attached_form_id' => 2, 'description' => json_encode([
                'header' => 'Dubai',
                'content' => 'Sample time lapse slider'
            ])),
            array('attachment_id' => 25, 'attachable_id' => 7, 'attachable_type' => "App\Argo\Project", 'attached_at' => '2009-01-01 00:00:00', 'attached_form_id' => 2, 'description' => json_encode([
                'header' => 'Dubai',
                'content' => 'Sample time lapse slider'
            ])),
            array('attachment_id' => 26, 'attachable_id' => 7, 'attachable_type' => "App\Argo\Project", 'attached_at' => '2010-01-01 00:00:00', 'attached_form_id' => 2, 'description' => json_encode([
                'header' => 'Dubai',
                'content' => 'Sample time lapse slider'
            ])),
            array('attachment_id' => 27, 'attachable_id' => 7, 'attachable_type' => "App\Argo\Project", 'attached_at' => '2011-01-01 00:00:00', 'attached_form_id' => 2, 'description' => json_encode([
                'header' => 'Dubai',
                'content' => 'Sample time lapse slider'
            ])),
            array('attachment_id' => 28, 'attachable_id' => 7, 'attachable_type' => "App\Argo\Project", 'attached_at' => '2012-01-01 00:00:00', 'attached_form_id' => 2, 'description' => json_encode([
                'header' => 'Dubai',
                'content' => 'Sample time lapse slider'
            ])),
            array('attachment_id' => 29, 'attachable_id' => 7, 'attachable_type' => "App\Argo\Project", 'attached_at' => '2013-01-01 00:00:00', 'attached_form_id' => 2, 'description' => json_encode([
                'header' => 'Dubai',
                'content' => 'Sample time lapse slider'
            ])),
            array('attachment_id' => 30, 'attachable_id' => 7, 'attachable_type' => "App\Argo\Project", 'attached_at' => '2014-01-01 00:00:00', 'attached_form_id' => 2, 'description' => json_encode([
                'header' => 'Dubai',
                'content' => 'Sample time lapse slider'
            ])),
            array('attachment_id' => 31, 'attachable_id' => 7, 'attachable_type' => "App\Argo\Project", 'attached_at' => '2015-01-01 00:00:00', 'attached_form_id' => 2, 'description' => json_encode([
                'header' => 'Dubai',
                'content' => 'Sample time lapse slider'
            ])),
            array('attachment_id' => 32, 'attachable_id' => 7, 'attachable_type' => "App\Argo\Project", 'attached_at' => '2016-01-01 00:00:00', 'attached_form_id' => 2, 'description' => json_encode([
                'header' => 'Dubai',
                'content' => 'Sample time lapse slider'
            ])),
        );
        $this->command->info("attachables : Seeding " . count($records) . " records...");
        foreach($records as $record) {
            DB::table('attachables')->insert($record);
        }
    }
}