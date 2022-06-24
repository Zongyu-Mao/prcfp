<?php

namespace App\Listeners\Encyclopedia\EntryDiscussion;

use App\Events\Encyclopedia\EntryDiscussion\EntryAdvisementAcceptedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Encyclopedia\EntryDiscussion\EntryAdvisementAcceptedNotification;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryDiscussion\EntryDiscussionEvent;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryAdvisementAcceptedListener
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
     * @param  EntryAdvisementAcceptedEvent  $event
     * @return void
     */
    public function handle(EntryAdvisementAcceptedEvent $event)
    {
        // 词条反对的讨论被接受后，仅通知讨论的作者
        $advise = $event->entryAdvise;
        $entry = Entry::find($advise->eid);
        // 添加事件到用户动态
        $behavior = '接受了百科建议讨论：';
        $objectName = $advise->title;
        $objectURL = '/encyclopedia/discussion/'.$entry->id.'/'.$entry->title.'#discussionAdvise'.$advise->id;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($advise->recipient_id,$advise->recipient,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 词条添加热度记录
        $b_id = 52;
        EntryTemperatureRecord::recordAdd($entry->id,$advise->recipient_id,$b_id,$createtime);
        //建议被接受后，作者的积分和成长值+20
        User::expAndGrowValue($advise->author_id,'20','20');
        //建议被接受后，操作者的积分和成长值+20
        User::expAndGrowValue($advise->recipient_id,'20','20');
        // 添加讨论事件
        EntryDiscussionEvent::discussionEventAdd($entry->id,$advise->recipient_id,$advise->recipient,'接受了['.$advise->author.']提出的建议。');
        // 给反对作者发送通知
        User::find($advise->author_id)->notify(new EntryAdvisementAcceptedNotification($advise));
    }
}
