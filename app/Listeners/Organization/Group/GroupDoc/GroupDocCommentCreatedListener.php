<?php

namespace App\Listeners\Organization\Group\GroupDoc;

use App\Events\Organization\Group\GroupDoc\GroupDocCommentCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Organization\Group\GroupDoc;
use App\Home\Organization\Group\GroupDoc\GroupDocComment;
use App\Home\Organization\Group\GroupDoc\GroupDocEvent;
use App\Notifications\Organization\Group\GroupDoc\GroupDocCommentCreatedNotification;
use App\Models\User;
use Carbon\Carbon;

class GroupDocCommentCreatedListener
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
     * @param  GroupDocCommentCreatedEvent  $event
     * @return void
     */
    public function handle(GroupDocCommentCreatedEvent $event)
    {
        //评论发表，只要添加到事件并通知文档创建者
        $comment = $event->groupDocComment;
        $doc = GroupDoc::find($comment->did);
        $user = User::find($doc->creator_id);
        GroupDocEvent::groupDocEventAdd($doc->id,$user->id,$user->username,'发表了文档评论。',Carbon::now());
        $userToNotification = User::find($doc->creator_id);
        $userToNotification->notify(new GroupDocCommentCreatedNotification($comment));
    }
}
