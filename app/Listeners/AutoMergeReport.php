<?php

namespace App\Listeners;

use App\Events\ReportCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Services\ReportService;

class AutoMergeReport
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ReportCreated  $event
     * @return void
     */
    public function handle(ReportCreated $event)
    {
        if(config('argodf.auto_approved_submitted_report')) {
            $reportService = new ReportService($event->reportId);
            $reportService->autoMerge();
            \Log::info("Report report_id:$event->reportId has been auto approved.");
        }

        return $event->reportId;
    }
}
