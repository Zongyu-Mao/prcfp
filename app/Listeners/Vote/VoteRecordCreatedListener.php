<?php

namespace App\Listeners\Vote;

use App\Events\Vote\VoteRecordCreatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class VoteRecordCreatedListener
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
     * @param  VoteRecordCreatedEvent  $event
     * @return void
     */
    public function handle(VoteRecordCreatedEvent $event)
    {
        //
    }
}
