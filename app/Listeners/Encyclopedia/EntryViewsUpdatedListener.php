<?php

namespace App\Listeners\Encyclopedia;

use App\Events\Encyclopedia\EntryViewsUpdatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Encyclopedia\Recommend\EntryTemperature;
use App\Home\Personnel\Behavior;
use Illuminate\Support\Facades\Auth;

class EntryViewsUpdatedListener
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
     * @param  EntryViewsUpdatedEvent  $event
     * @return void
     */
    public function handle(EntryViewsUpdatedEvent $event)
    {
        //浏览量的增加会引起热度的更新
        $entry = $event->entry;
        // 添加热度记录
        $t = Behavior::find(3)->score; 
        $ext = EntryTemperature::where('eid',$entry->id)->first();
        $old_tem = $ext->temperature;
        $tem = $old_tem + $t;
        EntryTemperature::recommendationUpdate($ext->id,$tem);
    }
}
