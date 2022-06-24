<?php

namespace App\Listeners\Encyclopedia\EntryCooperation;

use App\Events\Encyclopedia\EntryCooperation\EntryCooperationDiscussionCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationDiscussion;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationEvent;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Models\User;

class EntryCooperationDiscussionCreatedListener
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
     * @param  EntryCooperationDiscussionCreatedEvent  $event
     * @return void
     */
    public function handle(EntryCooperationDiscussionCreatedEvent $event)
    {
        //成员发表讨论后，写入协作事件
        $discussion = $event->entryCooperationDiscussion;
        $entry = Entry::find(EntryCooperation::find($discussion->cooperation_id)->eid);
        User::expAndGrowValue($discussion->author_id,5,5);
        // 词条添加热度记录
        $b_id = 19;
        EntryTemperatureRecord::recordAdd($entry->id,$discussion->author_id,$b_id,$discussion->created_at);
        // 添加协作事件
        EntryCooperationEvent::cooperationEventAdd($discussion->cooperation_id,$discussion->author_id,$discussion->author,'发表了对协作计划的讨论。');


    }
}
