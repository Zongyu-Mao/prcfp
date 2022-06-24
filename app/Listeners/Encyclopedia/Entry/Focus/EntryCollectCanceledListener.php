<?php

namespace App\Listeners\Encyclopedia\Entry\Focus;

use App\Events\Encyclopedia\Entry\Focus\EntryCollectCanceledEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EntryCollectCanceledListener
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
     * @param  EntryCollectCanceledEvent  $event
     * @return void
     */
    public function handle(EntryCollectCanceledEvent $event)
    {
        //取消收藏后的热度更新
        $focus = $event->entryCollectUser;
        // 添加热度记录
        $b_id = 7;

        EntryTemperatureRecord::recordAdd($focus->eid,auth('api')->user()->id,$b_id,Carbon::now());
    }
}
