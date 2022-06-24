<?php

namespace App\Listeners\Encyclopedia\EntryDebate;

use App\Events\Encyclopedia\EntryDebate\EntryDebateBOpeningCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Encyclopedia\EntryDebate\EntryDebateBOpeningCreatedNotification;
use App\Notifications\Encyclopedia\EntryDebate\EntryDebateBOpeningCreatedToRefereeNotification;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryDebate\EntryDebateEvent;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryDebateBOpeningCreatedListener
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
     * @param  EntryDebateBOpeningCreatedEvent  $event
     * @return void
     */
    public function handle(EntryDebateBOpeningCreatedEvent $event)
    {
        //辩方开篇陈词后，需要写入词条辩论事件，并通知攻方辩方已经回应
        //添加事件到辩论事件表
        $debate = $event->entryDebate;
        $entry = Entry::find($debate->eid);
        $createtime = Carbon::now();
        EntryDebateEvent::debateEventAdd($debate->id,$debate->Bauthor_id,$debate->Bauthor,'回应了'.$debate->Aauthor.'发起的攻辩:['.$debate->title.']；发表了辩方开篇陈词'); 
        //发表了有效的立论及陈词后，积分和成长值+100
        User::expAndGrowValue($debate->Bauthor_id,'100','100');
        // 词条添加热度记录
        $b_id = 61;
        EntryTemperatureRecord::recordAdd($entry->id,$debate->Bauthor_id,$b_id,$createtime);
        // 写入用户动态
        $behavior = '写入辩方陈词，回应攻辩：';
        $objectName = $debate->title;
        $objectURL = '/encyclopedia/debate/'.$entry->id.'/'.$entry->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        
        UserDynamic::dynamicAdd($debate->Bauthor_id,$debate->Bauthor,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 发送通知给发布辩论用户
        User::find($debate->Aauthor_id)->notify(new EntryDebateBOpeningCreatedNotification($debate));
        if($debate->referee_id){
            User::find($debate->referee_id)->notify(new EntryDebateBOpeningCreatedToRefereeNotification($debate));
        }
    }
}
