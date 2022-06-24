<?php

namespace App\Listeners\Encyclopedia\EntryDebate\EntryDebateComment;

use App\Events\Encyclopedia\EntryDebate\EntryDebateComment\EntryDebateCommentCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Encyclopedia\EntryDebate\EntryDebateComment\EntryDebateCommentRepliedNotification;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryDebate\EntryDebateComment;
use App\Home\Encyclopedia\EntryDebate\EntryDebateEvent;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use Carbon\Carbon;
use App\Models\User;

class EntryDebateCommentCreatedListener
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
     * @param  EntryDebateCommentCreatedEvent  $event
     * @return void
     */
    public function handle(EntryDebateCommentCreatedEvent $event)
    {
        //
        $comment = $event->entryDebateComment;
        $entry = Entry::find($comment->eid);
        $createtime = Carbon::now();
        //添加事件到辩论事件表
        EntryDebateEvent::debateEventAdd($comment->debate_id,$comment->author_id,$comment->getAuthor->username,'发表了新的评论:<'.$comment->title.'>。');
        // 词条添加热度记录
        if($comment->pid == 0){
            $b_id = 69;
        }else{
            $b_id = 70;
            User::find(EntryDebateComment::find($comment->pid)->author_id)->notify(new EntryDebateCommentRepliedNotification($comment));
        }
        EntryTemperatureRecord::recordAdd($entry->id,$comment->author_id,$b_id,$createtime);
        
    }
}
