<?php

namespace App\Listeners\Encyclopedia\EntryDebate\EntryDebateGiveLike;

use App\Events\Encyclopedia\EntryDebate\EntryDebateGiveLike\EntryDebateGivenLikeEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Encyclopedia\EntryDebate;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryDebate\EntryDebateEvent;
use App\Notifications\Encyclopedia\EntryDebate\EntryDebateGiveLike\EntryDebateGivenLikeNotification;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryDebateGivenLikeListener
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
     * @param  EntryDebateGivenLikeEvent  $event
     * @return void
     */
    public function handle(EntryDebateGivenLikeEvent $event)
    {
        //用户点赞后，记录事件，发送通知给被点赞方
        $debate = EntryDebate::find($event->entryDebateStarRecord->debate_id);
        $entry = Entry::find($debate->eid);
        // 判断立场并更新辩论表的点赞数
        if($event->entryDebateStarRecord->star == '0'){
            $standpoint = '送了一颗红星星给';
        }elseif($event->entryDebateStarRecord->star == '1'){
            $standpoint = '送了一颗黑星星给';
        }
        // 判断对象
        if($event->entryDebateStarRecord->object == '0'){
            $starObject = '攻方。';
            $notify_id = $debate->Aauthor_id;
        }elseif($event->entryDebateStarRecord->object == '1'){
            $starObject = '辩方。';
            $notify_id = $debate->Bauthor_id;
        }elseif($event->entryDebateStarRecord->object == '2'){
            $starObject = '裁判。';
            $notify_id = $debate->referee_id;
        }
        //添加事件到辩论事件表
        EntryDebateEvent::debateEventAdd($event->entryDebateStarRecord->debate_id,$event->entryDebateStarRecord->user_id,$event->entryDebateStarRecord->username,$standpoint.$starObject); 
        // 添加事件到用户动态
        $behavior = $standpoint.$starObject.'在攻辩：';
        $objectName = $debate->title;
        $objectURL = '/encyclopedia/debate/'.$entry->id.'/'.$entry->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($event->entryDebateStarRecord->user_id,$event->entryDebateStarRecord->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 发送通知给被点赞方
        User::find($notify_id)->notify(new EntryDebateGivenLikeNotification($event->entryDebateStarRecord));
    }
}
