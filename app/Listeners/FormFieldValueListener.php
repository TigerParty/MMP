<?php

namespace App\Listeners;

use App\Events\ProjectValueUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Services\ValueService;

class FormFieldValueListener
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
     * Register the listeners for the subscriber.
     *
     */
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\ProjectValueUpdated',
            'App\Listeners\FormFieldValueListener@onProjectValueUpdated'
        );
    }

    /**
     * Handle project value updated event.
     *
     * @param  ProjectValueUpdated  $event
     * @return void
     */
    public function onProjectValueUpdated(ProjectValueUpdated $event)
    {
        $valueService = new ValueService($event->projectId, 'project');
        $valueService->calcFormula()->updateValue();

        \Log::info("[FormFieldValueListener] ProjectValueUpdated event triggered.");
    }
}
