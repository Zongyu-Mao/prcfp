<?php

namespace App\Listeners\Personal\Relationship;

use App\Events\Personal\Relationship\FriendActivityInvitationCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Personal\Relationship\FriendActivityInvitationCreatedNotification;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationEvent;
use App\Home\Publication\ArticleCooperation\ArticleCooperationEvent;
use App\Home\Examination\ExamCooperation\ExamCooperationEvent;
use App\Home\Organization\Group\GroupEvent;
use App\Models\User;

class FriendActivityInvitationCreatedListener
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
     * @param  FriendActivityInvitationCreatedEvent  $event
     * @return void
     */
    public function handle(FriendActivityInvitationCreatedEvent $event)
    {
        $record = $event->friendActivityInvitationRecord;
        if($record->type == 1){
            // 写入百科词条协作事件
            EntryCooperationEvent::cooperationEventAdd($record->type_id,$record->user_id,$record->username,'正在邀请['.$record->invite_username.']进入协作小组。');
        }elseif($record->type == 2){
            // 写入著作协作事件
            ArticleCooperationEvent::cooperationEventAdd($record->type_id,$record->user_id,$record->username,'正在邀请['.$record->invite_username.']进入协作小组。');
        }elseif($record->type == 3){
            // 写入试卷协作事件
            ExamCooperationEvent::cooperationEventAdd($record->type_id,$record->user_id,$record->username,'正在邀请['.$record->invite_username.']进入协作小组。');
        }elseif($record->type == 4){
            // 写入组织事件
            GroupEvent::groupEventAdd($record->type_id,$record->user_id,$record->username,'正在邀请['.$record->invite_username.']进入组织。');
        }
        //邀请发送后，通知被邀请用户邀请的内容
        User::find($record->invite_id)->notify(new FriendActivityInvitationCreatedNotification($record));
    }
}
