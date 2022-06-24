<?php

namespace App\Listeners\Encyclopedia\Entry\Focus;

use App\Events\Encyclopedia\Entry\Focus\EntryFocusedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EntryFocusedListener
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
     * @param  EntryFocusedEvent  $event
     * @return void
     */
    public function handle(EntryFocusedEvent $event)
    {
        //关注后的热度更新
        $focus = $event->entryFocusUser;
        // 添加热度记录
        $b_id = 4;

        EntryTemperatureRecord::recordAdd($focus->eid,$focus->user_id,$b_id,Carbon::now());
    }
}
