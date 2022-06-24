<?php

namespace App\Listeners\Personnel\MedalSuit;

use App\Events\Personnel\MedalSuit\MedalSuitCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Personnel\MedalSuit;

class MedalSuitCreatedListener
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
     * @param  MedalSuitCreatedEvent  $event
     * @return void
     */
    public function handle(MedalSuitCreatedEvent $event)
    {
        //套件创建，目前只需写入事件
    }
}
