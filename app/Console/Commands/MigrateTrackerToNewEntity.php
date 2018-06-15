<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TrackerService;
use App\Argo\ProjectValue;
use App\Argo\Tracker;
use DB;

class MigrateTrackerToNewEntity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'argo:migrate-trackers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate trackers from dynamic form to new Tracker table';

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
        $this->info('Start migrate trakers...');

        $originTrackers = ProjectValue::select('value')
            ->leftJoin('form_field', 'form_field.id', '=', 'project_value.form_field_id')
            ->leftJoin('field_template', 'form_field.field_template_id', '=', 'field_template.id')
            ->leftJoin('project', 'project.id', '=', 'project_value.project_id')
            ->where('field_template.key', '=', 'gps_tracker')
            ->whereNull('project.deleted_at')
            ->get();

        try {
            DB::beginTransaction();
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
            DB::commit();

            $this->info('All trackers migrated!');
        } catch (\Exception $e) {
            DB::rollback();
            $this->error($e);
        }
    }
}
