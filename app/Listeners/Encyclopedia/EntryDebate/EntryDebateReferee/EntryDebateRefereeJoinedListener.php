<?php

namespace App\Listeners\Encyclopedia\EntryDebate\EntryDebateReferee;

use App\Events\Encyclopedia\EntryDebate\EntryDebateReferee\EntryDebateRefereeJoinedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryDebate\EntryDebateEvent;
use App\Notifications\Encyclopedia\EntryDebate\EntryDebateReferee\EntryDebateRefereeJoinedNotification;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryDebateRefereeJoinedListener
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
     * @param  EntryDebateRefereeJoinedEvent  $event
     * @return void
     */
    public function handle(EntryDebateRefereeJoinedEvent $event)
    {
        //裁判加入仅需写入辩论事件，用户动态并通知攻辩双方
        //添加事件到辩论事件表
        $debate = $event->entryDebate;
        $entry = Entry::find($debate->eid);
        $createtime = Carbon::now();
        EntryDebateEvent::debateEventAdd($debate->id,$debate->referee_id,$debate->referee,'成为裁判'); 
        // 词条添加热度记录
        $b_id = 62;
        EntryTemperatureRecord::recordAdd($entry->id,$debate->referee_id,$b_id,$createtime);
        // 添加事件到用户动态
        $behavior = '成为裁判，在攻辩：';
        $objectName = $debate->title;
        $objectURL = '/encyclopedia/debate/'.$entry->id.'/'.$entry->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        
        UserDynamic::dynamicAdd($debate->referee_id,$debate->referee,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 发送通知给辩论攻方
        User::find($debate->Aauthor_id)->notify(new EntryDebateRefereeJoinedNotification($debate));
        // 发送通知给辩论辩方
        User::find($debate->Bauthor_id)->notify(new EntryDebateRefereeJoinedNotification($debate));
    }
}
