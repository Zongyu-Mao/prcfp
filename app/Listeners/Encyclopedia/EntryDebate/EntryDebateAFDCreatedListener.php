<?php

namespace App\Listeners\Encyclopedia\EntryDebate;

use App\Events\Encyclopedia\EntryDebate\EntryDebateAFDCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Encyclopedia\EntryDebate\EntryDebateAFDCreatedNotification;
use App\Notifications\Encyclopedia\EntryDebate\EntryDebateAFDCreatedToRefereeNotification;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryDebate\EntryDebateEvent;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryDebateAFDCreatedListener
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
     * @param  EntryDebateAFDCreatedEvent  $event
     * @return void
     */
    public function handle(EntryDebateAFDCreatedEvent $event)
    {
        //攻方写入自由辩论内容后，需要写入词条辩论事件，并通知辩方攻方已经回应
        //添加事件到辩论事件表
        $debate = $event->entryDebate;
        $entry = Entry::find($debate->eid);
        $createtime = Carbon::now();
        EntryDebateEvent::debateEventAdd($debate->id,$debate->Aauthor_id,$debate->Aauthor,'在其发起的攻辩:['.$debate->title.']发表了攻方自由辩论。'); 
        //发表了有效的立论及陈词后，积分和成长值+100
        User::expAndGrowValue($debate->Aauthor_id,'100','100');
        // 词条添加热度记录
        $b_id = 60;
        EntryTemperatureRecord::recordAdd($entry->id,$debate->Aauthor_id,$b_id,$createtime);
        // 写入用户动态
        $behavior = '写入攻方自由辩论，在发起的攻辩：';
        $objectName = $debate->title;
        $objectURL = '/encyclopedia/debate/'.$entry->id.'/'.$entry->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        
        UserDynamic::dynamicAdd($debate->Aauthor_id,$debate->Aauthor,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 发送通知给辩方用户
        User::find($debate->Bauthor_id)->notify(new EntryDebateAFDCreatedNotification($debate));
        if($debate->referee_id){
            User::find($debate->referee_id)->notify(new EntryDebateAFDCreatedToRefereeNotification($debate));
        }
    }
}
