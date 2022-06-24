<?php

namespace App\Listeners\Encyclopedia\EntryDebate;

use App\Events\Encyclopedia\EntryDebate\EntryDebateBFDCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Encyclopedia\EntryDebate\EntryDebateBFDCreatedNotification;
use App\Notifications\Encyclopedia\EntryDebate\EntryDebateBFDCreatedToRefereeNotification;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryDebate\EntryDebateEvent;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryDebateBFDCreatedListener
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
     * @param  EntryDebateBFDCreatedEvent  $event
     * @return void
     */
    public function handle(EntryDebateBFDCreatedEvent $event)
    {
        //辩方写入自由辩论内容后，需要写入词条辩论事件，并通知攻方辩方已经回应
        //添加事件到辩论事件表
        $debate = $event->entryDebate;
        $entry = Entry::find($debate->eid);
        $createtime = Carbon::now();
        EntryDebateEvent::debateEventAdd($debate->id,$debate->Bauthor_id,$debate->Bauthor,'在攻辩:['.$debate->title.']发表了辩方自由辩论。'); 
        //发表了有效的立论及陈词后，积分和成长值+100
        User::expAndGrowValue($debate->Bauthor_id,'100','100');
        // 词条添加热度记录
        $b_id = 61;
        EntryTemperatureRecord::recordAdd($entry->id,$debate->Bauthor_id,$b_id,$createtime);
        // 写入用户动态
        $behavior = '写入辩方自由辩论，在攻辩：';
        $objectName = $debate->title;
        $objectURL = '/encyclopedia/debate/'.$entry->id.'/'.$entry->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        
        UserDynamic::dynamicAdd($debate->Bauthor_id,$debate->Bauthor,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 发送通知给攻方用户
        User::find($debate->Aauthor_id)->notify(new EntryDebateBFDCreatedNotification($debate));
        if($debate->referee_id){
            User::find($debate->referee_id)->notify(new EntryDebateBFDCreatedToRefereeNotification($debate));
        }
    }
}
