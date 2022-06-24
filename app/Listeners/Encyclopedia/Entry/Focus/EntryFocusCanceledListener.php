<?php

namespace App\Listeners\Encyclopedia\Entry\Focus;

use App\Events\Encyclopedia\Entry\Focus\EntryFocusCanceledEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EntryFocusCanceledListener
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
     * @param  EntryFocusCanceledEvent  $event
     * @return void
     */
    public function handle(EntryFocusCanceledEvent $event)
    {
        //关注后的热度更新
        $focus = $event->entryFocusUser;
        // 添加热度记录
        $b_id = 5;

        EntryTemperatureRecord::recordAdd($focus->eid,auth('api')->user()->id,$b_id,Carbon::now());
    }
}
