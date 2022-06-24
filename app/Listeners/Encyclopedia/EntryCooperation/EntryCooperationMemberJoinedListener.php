<?php

namespace App\Listeners\Encyclopedia\EntryCooperation;

use App\Events\Encyclopedia\EntryCooperation\EntryCooperationMemberJoinedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationEvent;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\EntryDynamic;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\Encyclopedia\Entry;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Home\Cooperation\EntryContributeValue;

class EntryCooperationMemberJoinedListener
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
     * @param  EntryCooperationMemberJoinedEvent  $event
     * @return void
     */
    public function handle(EntryCooperationMemberJoinedEvent $event)
    {
        // 成功写入协作成员后，触发事件：词条协作事件，词条动态，用户动态；此处暂时不产生通知
        $ecu = $event->entryCooperationUser;
        $cooperation = EntryCooperation::find($ecu->cooperation_id);
        $entry = Entry::find($cooperation->eid);
        $user = User::find($ecu->user_id);
        $createtime = Carbon::now();
        // 写入贡献表
        EntryContributeValue::contributeAdd($ecu->cooperation_id,$ecu->user_id,0);
        // 词条添加热度记录
        $b_id = 16;
        EntryTemperatureRecord::recordAdd($entry->id,$user->id,$b_id,$createtime);
        // 写入协作事件
        EntryCooperationEvent::cooperationEventAdd($cooperation->id,$user->id,$user->username,'成功加入协作小组，大家合作愉快。');
        // 添加事件到用户动态
        $behavior = '加入了百科协作计划：';
        $objectName = $cooperation->title;
        $objectURL = '/encyclopedia/cooperation/'.$entry->id.'/'.$entry->title;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        UserDynamic::dynamicAdd($user->id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $entryBehavior = '新增协作计划成员：['.$user->username.']';
        EntryDynamic::dynamicAdd($entry->id,$entry->title,$entryBehavior,$objectName,$objectURL,$createtime);
    }
}
