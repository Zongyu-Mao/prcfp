<?php

namespace App\Listeners\Personnel\Medal;

use App\Events\Personnel\Medal\MedalCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Personnel\Medal;
use App\Home\Personnel\MedalSuit;

class MedalCreatedListener
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
     * @param  MedalCreatedEvent  $event
     * @return void
     */
    public function handle(MedalCreatedEvent $event)
    {
        //功章创建后，写入事件**************，判断套件是否满了（这个在控制器里也会判断—
        $medal = $event->medal;
        $suit = $medal->getSuit;
        if(count($suit->getMedals) == $suit->amount){
            $status = 2;
            MedalSuit::statusUpdate($suit->id,$status);
        }
        // 写入事件
    }
}
