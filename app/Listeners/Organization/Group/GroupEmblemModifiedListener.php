<?php

namespace App\Listeners\Organization\Group;

use App\Events\Organization\Group\GroupEmblemModifiedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GroupEmblemModifiedListener
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
     * @param  GroupEmblemModifiedEvent  $event
     * @return void
     */
    public function handle(GroupEmblemModifiedEvent $event)
    {
        //
    }
}
