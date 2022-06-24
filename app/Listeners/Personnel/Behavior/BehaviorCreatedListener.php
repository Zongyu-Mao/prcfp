<?php

namespace App\Listeners\Personnel\Behavior;

use App\Events\Personnel\Behavior\BehaviorCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Models\User;
use Carbon\Carbon;
use App\Home\UserDynamic;
use App\Home\Announcement;
use Illuminate\Support\Facades\Auth;

class BehaviorCreatedListener
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
     * @param  BehaviorCreatedEvent  $event
     * @return void
     */
    public function handle(BehaviorCreatedEvent $event)
    {
        //热度行为的创建，第一步已经写入数据库后，这里触发缓存的更改，如此不能使用队列
        // 如果到时候要反过来先改缓存再写数据库再说~~~
        // 虽然名称中采用了created，但是modify也调用了这个事件
        $behavior = $event->behavior;
        // behavior应该采用hash
        $user = auth('api')->user();
        $a = ($behavior->created_at==$behavior->updated_at);
        $b = ($a?'创建':'修改').'了行为：《'.$behavior->name.'》。';
        $objectName = $behavior->name;
        $objectURL = '/personnel/behavior';
        $fromName = '[人事]行为';
        $fromURL = $objectURL;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($user->id,$user->username,$b,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 发布公告5代表创建
        if($a)Announcement::announcementAdd(6,5,'行为<'.$behavior->name.'>已经创建。',$objectURL,$createtime);

    }
}
