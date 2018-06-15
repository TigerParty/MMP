<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TrackerService;
use App\Argo\ProjectValue;
use App\Argo\Tracker;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use DB;


class MigrateDbV4ToV5 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'argo:migrate-v4';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate database v4 schema to v5';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Updating migration table and fix missed match problems...");
        $this->upateMigrationMissmatch();

        $this->info('Migrating tracker data from v4 project values...');
        $this->migrateTrackerDataFromProjectValue();

        $this->info("Updating attachable namespaces...");
        $this->updateAttachableNamespace();

        $this->info("Updating notification namespaces...");
        $this->updateNotificationNamespaces();
    }

    private function updateNotificationNamespaces()
    {
        DB::table('notification')
            ->where('notify_type', '=', 'App\\Argodf\\Project')
            ->update(['notify_type' => 'App\\Argo\\Project']);
        DB::table('notification')
            ->where('notify_type', '=', 'App\\Argodf\\Report')
            ->update(['notify_type' => 'App\\Argo\\Report']);

        DB::table('notification_sms')
            ->where('notify_type', '=', 'App\\Argodf\\Project')
            ->update(['notify_type' => 'App\\Argo\\Project']);
        DB::table('notification_sms')
            ->where('notify_type', '=', 'App\\Argodf\\CitizenSMSReply')
            ->update(['notify_type' => 'App\\Argo\\CitizenSMSReply']);

    }

    private function upateMigrationMissmatch()
    {
        // Refresh migrations table, to fit the v5 migrations
        DB::statement('TRUNCATE TABLE migrations');
        Schema::table('migrations', function($table){
            $table->increments('id');
        });

        DB::statement('ALTER TABLE `migrations` MODIFY COLUMN `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT FIRST');

        DB::table('migrations')->insert([
            ['id' => 1, 'migration' => '2018_03_08_030129_create_survey_table', 'batch' => 1],
            ['id' => 2, 'migration' => '2018_03_08_030148_create_report_raw_table', 'batch' => 1],
            ['id' => 3, 'migration' => '2018_03_08_030204_create_report_citizen_table', 'batch' => 1],
            ['id' => 4, 'migration' => '2018_03_08_030253_create_region_label_table', 'batch' => 1],
            ['id' => 5, 'migration' => '2018_03_08_030355_create_permission_level_table', 'batch' => 1],
            ['id' => 6, 'migration' => '2018_03_08_030421_create_indicator_table', 'batch' => 1],
            ['id' => 7, 'migration' => '2018_03_08_030458_create_field_template_table', 'batch' => 1],
            ['id' => 8, 'migration' => '2018_03_08_030522_create_dynamic_form_table', 'batch' => 1],
            ['id' => 9, 'migration' => '2018_03_08_030530_create_dynamic_config_table', 'batch' => 1],
            ['id' => 10, 'migration' => '2018_03_08_030553_create_citizen_sms_table', 'batch' => 1],
            ['id' => 11, 'migration' => '2018_03_08_030647_create_attachment_table', 'batch' => 1],
            ['id' => 12, 'migration' => '2018_03_08_030823_create_user_table', 'batch' => 1],
            ['id' => 13, 'migration' => '2018_03_08_030838_create_tracker_table', 'batch' => 1],
            ['id' => 14, 'migration' => '2018_03_08_030903_create_reporter_location_table', 'batch' => 1],
            ['id' => 15, 'migration' => '2018_03_08_031036_create_region_table', 'batch' => 1],
            ['id' => 16, 'migration' => '2018_03_08_031306_create_form_field_table', 'batch' => 1],
            ['id' => 17, 'migration' => '2018_03_08_031413_create_container_table', 'batch' => 1],
            ['id' => 18, 'migration' => '2018_03_08_031512_create_citizen_sms_reply_table', 'batch' => 1],
            ['id' => 19, 'migration' => '2018_03_08_031718_create_aggregation_table', 'batch' => 1],
            ['id' => 20, 'migration' => '2018_03_08_031905_create_project_status_table', 'batch' => 1],
            ['id' => 21, 'migration' => '2018_03_08_031909_create_project_table', 'batch' => 1],
            ['id' => 22, 'migration' => '2018_03_08_031930_create_project_value_table', 'batch' => 1],
            ['id' => 23, 'migration' => '2018_03_08_031956_create_report_table', 'batch' => 1],
            ['id' => 24, 'migration' => '2018_03_08_032014_create_report_value_table', 'batch' => 1],
            ['id' => 25, 'migration' => '2018_03_08_032138_create_notification_table', 'batch' => 1],
            ['id' => 26, 'migration' => '2018_03_08_032141_create_notification_sms_table', 'batch' => 1],
            ['id' => 27, 'migration' => '2018_03_08_032748_create_attachables_table', 'batch' => 1],
            ['id' => 28, 'migration' => '2018_03_08_033050_create_relation_field_filter_container_table', 'batch' => 1],
            ['id' => 29, 'migration' => '2018_03_08_033135_create_relation_project_belongs_region_table', 'batch' => 1],
            ['id' => 30, 'migration' => '2018_03_08_033212_create_relation_project_image_container_table', 'batch' => 1],
            ['id' => 31, 'migration' => '2018_03_08_033245_create_relation_user_own_project_table', 'batch' => 1],
            ['id' => 32, 'migration' => '2018_03_08_093716_create_category_table', 'batch' => 1],
            ['id' => 33, 'migration' => '2018_03_08_093719_create_relation_project_belong_category', 'batch' => 1],
            ['id' => 34, 'migration' => '2018_03_08_093722_create_chart_table', 'batch' => 1],
            ['id' => 35, 'migration' => '2018_03_08_093741_create_relation_project_has_chart', 'batch' => 1],
            ['id' => 36, 'migration' => '2018_03_08_094511_create_queue_table', 'batch' => 1],
            ['id' => 37, 'migration' => '2018_03_08_095048_create_queue_failed_table', 'batch' => 1],
            ['id' => 38, 'migration' => '2018_03_08_095505_create_web_components_table', 'batch' => 1],
            ['id' => 39, 'migration' => '2018_03_09_024727_create_relation_project_has_form', 'batch' => 1],
        ]);

        // created_at, updated_at default value
        DB::statement('ALTER TABLE `tracker`
            CHANGE `created_at` `created_at` TIMESTAMP  NULL DEFAULT NULL,
            CHANGE `updated_at` `updated_at` TIMESTAMP  NULL DEFAULT NULL
        ');

        DB::statement('ALTER TABLE `user`
            CHANGE `created_at` `created_at` TIMESTAMP  NULL DEFAULT NULL,
            CHANGE `updated_at` `updated_at` TIMESTAMP  NULL DEFAULT NULL
        ');

        DB::statement('ALTER TABLE `project`
            CHANGE `created_at` `created_at` TIMESTAMP  NULL DEFAULT NULL,
            CHANGE `updated_at` `updated_at` TIMESTAMP  NULL DEFAULT NULL
        ');

        DB::statement('ALTER TABLE `citizen_sms`
            CHANGE `created_at` `created_at` TIMESTAMP  NULL DEFAULT NULL,
            CHANGE `updated_at` `updated_at` TIMESTAMP  NULL DEFAULT NULL
        ');

        DB::statement('ALTER TABLE `report`
            CHANGE `created_at` `created_at` TIMESTAMP  NULL DEFAULT NULL,
            CHANGE `updated_at` `updated_at` TIMESTAMP  NULL DEFAULT NULL
        ');

        DB::statement('ALTER TABLE `report_citizen`
            CHANGE `created_at` `created_at` TIMESTAMP  NULL DEFAULT NULL,
            CHANGE `updated_at` `updated_at` TIMESTAMP  NULL DEFAULT NULL
        ');

        DB::statement('ALTER TABLE `report_raw`
            CHANGE `created_at` `created_at` TIMESTAMP  NULL DEFAULT NULL,
            CHANGE `updated_at` `updated_at` TIMESTAMP  NULL DEFAULT NULL
        ');

        DB::statement('ALTER TABLE `reporter_location`
            CHANGE `created_at` `created_at` TIMESTAMP  NULL DEFAULT NULL,
            CHANGE `updated_at` `updated_at` TIMESTAMP  NULL DEFAULT NULL
        ');

        DB::statement('ALTER TABLE `notification`
            CHANGE `created_at` `created_at` TIMESTAMP  NULL DEFAULT NULL,
            CHANGE `updated_at` `updated_at` TIMESTAMP  NULL DEFAULT NULL
        ');

        DB::statement('ALTER TABLE `notification_sms`
            CHANGE `created_at` `created_at` TIMESTAMP  NULL DEFAULT NULL,
            CHANGE `updated_at` `updated_at` TIMESTAMP  NULL DEFAULT NULL
        ');

        DB::statement('ALTER TABLE `survey`
            CHANGE `created_at` `created_at` TIMESTAMP  NULL DEFAULT NULL,
            CHANGE `updated_at` `updated_at` TIMESTAMP  NULL DEFAULT NULL
        ');

        DB::statement('ALTER TABLE `attachment`
            CHANGE `created_at` `created_at` TIMESTAMP  NULL DEFAULT NULL,
            CHANGE `updated_at` `updated_at` TIMESTAMP  NULL DEFAULT NULL
        ');

        DB::statement('ALTER TABLE `chart`
            CHANGE `created_at` `created_at` TIMESTAMP  NULL DEFAULT NULL,
            CHANGE `updated_at` `updated_at` TIMESTAMP  NULL DEFAULT NULL
        ');

        // Several fix for migrations
        Schema::table('citizen_sms', function($table){
            $table->boolean('is_read')->default(false);
            $table->softDeletes();
        });

        Schema::table('report_citizen', function($table){
            $table->boolean('is_read')->default(false);
            $table->softDeletes();
            $table->string('source', 16)->nullable()->change();
            $table->string('version', 16)->nullable()->change();
        });

        Schema::table('container', function($table){
            $table->text('uid_rule')->nullable();
            $table->text('card_rule')->nullable();
        });

        Schema::table('tracker', function (Blueprint $table) {
            $table->json('meta')->nullable()->after('path');
        });
    }

    private function updateAttachableNamespace()
    {
        DB::table('attachables')
            ->where('attachable_type', '=', 'App\\Argodf\\Report')
            ->update(['attachable_type' => 'App\\Argo\\Report']);
        DB::table('attachables')
            ->where('attachable_type', '=', 'App\\Argodf\\ReportRaw')
            ->update(['attachable_type' => 'App\\Argo\\ReportRaw']);
        DB::table('attachables')
            ->where('attachable_type', '=', 'App\\Argodf\\ReportCitizen')
            ->update(['attachable_type' => 'App\\Argo\\ReportCitizen']);
        DB::table('attachables')
            ->where('attachable_type', '=', 'App\\Argodf\\Project')
            ->update(['attachable_type' => 'App\\Argo\\Project']);
        DB::table('attachables')
            ->where('attachable_type', '=', 'App\\Argodf\\Tracker')
            ->update(['attachable_type' => 'App\\Argo\\Tracker']);
    }

    private function migrateTrackerDataFromProjectValue()
    {
        $originTrackers = ProjectValue::select('value')
            ->leftJoin('form_field', 'form_field.id', '=', 'project_value.form_field_id')
            ->leftJoin('field_template', 'form_field.field_template_id', '=', 'field_template.id')
            ->leftJoin('project', 'project.id', '=', 'project_value.project_id')
            ->where('field_template.key', '=', 'gps_tracker')
            ->whereNull('project.deleted_at')
            ->get();

        foreach ($originTrackers as $originTracker) {
            $trackerData = json_decode($originTracker->value);
            if(!$trackerData || count($trackerData->coordinates) == 0) {
                continue;
            }
            $newTracker = new Tracker;
            $newTracker->path = [$trackerData->coordinates];
            $newTracker->created_at = $trackerData->end_at;
            $newTracker->updated_at = $trackerData->end_at;
            $newTracker->save();

            $trackerService = new TrackerService($newTracker->id, 'tracker');
            $newTracker->meta = $trackerService->calcTrackerMeta($newTracker->path[0])->getResult();
            $newTracker->save();
        }
    }
}
