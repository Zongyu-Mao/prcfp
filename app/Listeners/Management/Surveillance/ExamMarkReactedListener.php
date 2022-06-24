<?php

namespace App\Listeners\Management\Surveillance;

use App\Events\Management\Surveillance\ExamMarkReactedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ExamMarkReactedListener
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
     * @param  ExamMarkReactedEvent  $event
     * @return void
     */
    public function handle(ExamMarkReactedEvent $event)
    {
        //
    }
}
