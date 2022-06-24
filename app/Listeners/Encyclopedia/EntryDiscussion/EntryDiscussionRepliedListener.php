<?php

namespace App\Listeners\Encyclopedia\EntryDiscussion;

use App\Events\Encyclopedia\EntryDiscussion\EntryDiscussionRepliedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Encyclopedia\EntryDiscussion\EntryDiscussionRepliedNotification;
use App\Home\Encyclopedia\EntryDiscussion;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryDiscussion\EntryDiscussionEvent;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryDiscussionRepliedListener
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
     * @param  EntryDiscussionRepliedEvent  $event
     * @return void
     */
    public function handle(EntryDiscussionRepliedEvent $event)
    {
        // 词条普通讨论被回复后，仅通知讨论的作者
        $discussion = $event->entryDiscussion;
        $entry = Entry::find($discussion->eid);
        $parentDiscussion = EntryDiscussion::find($discussion->pid);
        // 添加事件到用户动态
        $behavior = '回复百科普通讨论：';
        $objectName = $discussion->title;
        $objectURL = '/encyclopedia/discussion/'.$entry->id.'/'.$entry->title.'#discussion'.$discussion->id;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($discussion->author_id,$discussion->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 词条添加热度记录
        $b_id = 55;
        EntryTemperatureRecord::recordAdd($entry->id,$discussion->author_id,$b_id,$createtime);
        //讨论被回复后，回复者的积分和成长值+10
        User::expAndGrowValue($discussion->author_id,'10','10');
        // 添加讨论事件
        EntryDiscussionEvent::discussionEventAdd($entry->id,$discussion->author_id,$discussion->author,'回复了['.$parentDiscussion->author.']提出的讨论内容<'.$parentDiscussion->title.'>。');
        // 给反对作者发送通知
        User::find($parentDiscussion->author_id)->notify(new EntryDiscussionRepliedNotification($discussion));
    }
}
