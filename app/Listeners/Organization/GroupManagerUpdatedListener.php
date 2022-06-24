<?php

namespace App\Listeners\Organization;

use App\Events\Organization\GroupManagerUpdatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GroupManagerUpdatedListener
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
     * @param  GroupManagerUpdatedEvent  $event
     * @return void
     */
    public function handle(GroupManagerUpdatedEvent $event)
    {
        //
    }
}
