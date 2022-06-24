<?php

namespace App\Listeners\Encyclopedia\EntryDebate\EntryDebateReferee;

use App\Events\Encyclopedia\EntryDebate\EntryDebateReferee\EntryDebateAnalyseUpdatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryDebate\EntryDebateEvent;
use App\Notifications\Encyclopedia\EntryDebate\EntryDebateReferee\EntryDebateAnalyseUpdatedNotification;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryDebateAnalyseUpdatedListener
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
     * @param  EntryDebateAnalyseUpdatedEvent  $event
     * @return void
     */
    public function handle(EntryDebateAnalyseUpdatedEvent $event)
    {
        //裁判分析的更新，只需要写入事件，写入用户动态，通知攻辩双方
        //添加事件到辩论事件表
        $debate = $event->entryDebate;
        $entry = Entry::find($debate->eid);
        $createtime = Carbon::now();
        EntryDebateEvent::debateEventAdd($debate->id,$debate->referee_id,$debate->referee,'更新了裁判分析。'); 
        // 词条添加热度记录
        $b_id = 63;
        if(EntryTemperatureRecord::where([['eid',$entry->id],['user_id',$debate->referee_id],['behavior_id',$b_id]])->count() < 3){
             EntryTemperatureRecord::recordAdd($entry->id,$debate->referee_id,$b_id,$createtime);
        }
        // 添加事件到用户动态
        $behavior = '发表/更新了裁判分析，在攻辩：';
        $objectName = $debate->title;
        $objectURL = '/encyclopedia/debate/'.$entry->id.'/'.$entry->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($debate->referee_id,$debate->referee,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 发送通知给辩论攻方
        User::find($debate->Aauthor_id)->notify(new EntryDebateAnalyseUpdatedNotification($debate));
        // 发送通知给辩论辩方
        User::find($debate->Bauthor_id)->notify(new EntryDebateAnalyseUpdatedNotification($debate));
    }
}
