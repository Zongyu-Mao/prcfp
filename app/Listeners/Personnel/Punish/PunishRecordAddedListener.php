<?php

namespace App\Listeners\Personnel\Punish;

use App\Events\Personnel\Punish\PunishRecordAddedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PunishRecordAddedListener
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
     * @param  PunishRecordAddedEvent  $event
     * @return void
     */
    public function handle(PunishRecordAddedEvent $event)
    {
        //
    }
}
