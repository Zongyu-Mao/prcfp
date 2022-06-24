<?php

namespace App\Listeners\Management\Surveillance;

use App\Events\Management\Surveillance\WayCreatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Committee\Surveillance\Mark\MarkDisposeWayEvent;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WayCreatedListener
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
     * @param  WayCreatedEvent  $event
     * @return void
     */
    public function handle(WayCreatedEvent $event)
    {
        $way = $event->surveillanceMarkDisposeWay;
        $user = auth('api')->user();
        $createtime = Carbon::now();
        MarkDisposeWayEvent::disposeEventAdd($way->id,$user->id,$user->username,'新建了主内容标记处理方式['.$way->sort.']。',$createtime);
    }
}
