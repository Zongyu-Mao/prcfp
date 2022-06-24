<?php

namespace App\Listeners\Encyclopedia\EntryCooperation;

use App\Events\Encyclopedia\EntryCooperation\EntryCooperationMemberFiredEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationEvent;
use App\Notifications\Encyclopedia\EntryCooperation\EntryCooperationMemberFiredNotification;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Home\Cooperation\EntryContributeValue;

class EntryCooperationMemberFiredListener
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
     * @param  EntryCooperationMemberFiredEvent  $event
     * @return void
     */
    public function handle(EntryCooperationMemberFiredEvent $event)
    {
        // 组员被请出后，写入事件，并发送通知给被请出组员
        $user = $event->entryCooperationUser;
        $cooperation = EntryCooperation::find($user->cooperation_id);
        $entry = Entry::find($cooperation->eid);
        $crew = User::find($user->user_id);
        // 删除贡献value
        EntryContributeValue::contributeDelete($user->cooperation_id,$user->user_id);
        EntryCooperationEvent::cooperationEventAdd($cooperation->id,$cooperation->manage_id,$cooperation->manager,'请出组员<'.$crew->username.'>。');
        // 写入用户动态
        $behavior = '退出百科协作计划：';
        $objectName = $cooperation->title;
        $objectURL = '/encyclopedia/cooperation/'.$entry->id.'/'.$entry->title;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($crew->id,$crew->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 词条添加热度记录
        $b_id = 18;
        EntryTemperatureRecord::recordAdd($entry->id,$crew->id,$b_id,$createtime);
        // 通知***
        $crew->notify(new EntryCooperationMemberFiredNotification($user));
    }
}
