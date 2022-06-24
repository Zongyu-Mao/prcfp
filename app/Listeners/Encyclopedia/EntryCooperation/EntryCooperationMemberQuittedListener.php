<?php

namespace App\Listeners\Encyclopedia\EntryCooperation;

use App\Events\Encyclopedia\EntryCooperation\EntryCooperationMemberQuittedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationEvent;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Home\Cooperation\EntryContributeValue;

class EntryCooperationMemberQuittedListener
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
     * @param  EntryCooperationMemberQuittedEvent  $event
     * @return void
     */
    public function handle(EntryCooperationMemberQuittedEvent $event)
    {
        $user = $event->entryCooperationUser;
        // 组员退出后，写入事件，并发送通知给被请出组员
        $cooperation = EntryCooperation::find($user->cooperation_id);
        $entry = Entry::find($cooperation->eid);
        $crew = User::find($user->user_id);
        $createtime = Carbon::now();
        // 删除贡献value
        EntryContributeValue::contributeDelete($user->cooperation_id,$user->user_id);
        // 词条添加热度记录
        $b_id = 17;
        EntryTemperatureRecord::recordAdd($entry->id,$crew->id,$b_id,$createtime);
        EntryCooperationEvent::cooperationEventAdd($cooperation->id,$crew->id,$crew->username,'退出协作计划。');
        // 写入用户动态
        $behavior = '退出百科协作计划：';
        $objectName = $cooperation->title;
        $objectURL = '/encyclopedia/cooperation/'.$entry->id.'/'.$entry->title;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        
        UserDynamic::dynamicAdd($crew->id,$crew->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
    }
}
