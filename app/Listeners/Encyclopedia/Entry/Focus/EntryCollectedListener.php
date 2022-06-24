<?php

namespace App\Listeners\Encyclopedia\Entry\Focus;

use App\Events\Encyclopedia\Entry\Focus\EntryCollectedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EntryCollectedListener
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
     * @param  EntryCollectedEvent  $event
     * @return void
     */
    public function handle(EntryCollectedEvent $event)
    {
        //收藏后的热度更新
        $collect = $event->entryCollectUser;
        // 添加热度记录
        $b_id = 6;
        EntryTemperatureRecord::recordAdd($collect->eid,$collect->user_id,$b_id,Carbon::now());
    }
}
