<?php

namespace App\Listeners\Management\Surveillance;

use App\Events\Management\Surveillance\GroupMarkReactedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GroupMarkReactedListener
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
     * @param  GroupMarkReactedEvent  $event
     * @return void
     */
    public function handle(GroupMarkReactedEvent $event)
    {
        //
    }
}
