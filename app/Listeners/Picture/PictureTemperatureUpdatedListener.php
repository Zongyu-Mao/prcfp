<?php

namespace App\Listeners\Picture;

use App\Events\Picture\PictureTemperatureUpdatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PictureTemperatureUpdatedListener
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
     * @param  PictureTemperatureUpdatedEvent  $event
     * @return void
     */
    public function handle(PictureTemperatureUpdatedEvent $event)
    {
        $tem = $event->pictureTemperature;
        // zanwu neirong 
    }
}
