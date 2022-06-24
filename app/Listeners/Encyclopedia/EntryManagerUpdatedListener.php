<?php

namespace App\Listeners\Encyclopedia;

use App\Events\Encyclopedia\EntryManagerUpdatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Home\Encyclopedia\Recommend\EntryTemperature;
use App\Home\Encyclopedia\EntryDynamic;
use App\Home\Announcement;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryManagerUpdatedListener
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
     * @param  EntryManagerUpdatedEvent  $event
     * @return void
     */
    public function handle(EntryManagerUpdatedEvent $event)
    {
        $c = $event->entry;
        $user = $c->managerInfo;
        Announcement::announcementAdd(1,6,'词条['.$c->title.']已经变更新的自管理员['.$user->username.']。','/encyclopedia/reading/'.$c->id.'/'.$c->title,$c->updated_at);
        // 添加事件到用户动态
        $behavior = '接管自管理词条：';
        $objectName = $c->title;
        $objectURL = '/encyclopedia/reading/'.$c->id.'/'.$c->title;
        $fromName = '词条：'.$c->title;
        $fromURL = '/encyclopedia/reading/'.$c->id.'/'.$c->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($user->id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        $d_ehavior = '变更自管理员';
        EntryDynamic::dynamicAdd($c->id,$c->title,$d_ehavior,$objectName,$objectURL,$createtime);
    }
}
