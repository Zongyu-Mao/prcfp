<?php

namespace App\Listeners\Personal\Relationship;

use App\Events\Personal\Relationship\UserFriendApplicationAgreedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Personal\Relationship\FriendApplicationCompletedNotification;
use App\Notifications\Personal\Relationship\FriendApplicationAgreedNotification;
use App\Home\Personal\Relationship\UserFriendRelationship;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class UserFriendApplicationAgreedListener
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
     * @param  UserFriendApplicationAgreedEvent  $event
     * @return void
     */
    public function handle(UserFriendApplicationAgreedEvent $event)
    {
        //对方申请添加好友，通知用户该申请，并写入用户动态
        $r = $event->userFriendApplicationRecord;
        $user_id = $r->user_id;
        $username = $r->username;
        $application_id = $r->application_id;
        $application_username = $r->application_username;
        $createtime = Carbon::now();
        // 添加好友记录
        UserFriendRelationship::friendRelationshipAdd($user_id,$application_id,$createtime);
        // 添加好友、同意或拒绝好友不应添加到用户动态
        // 给被申请用户发送通知
        User::find($application_id)->notify(new FriendApplicationAgreedNotification($r));
        // 给同意用户发送通知
        User::find($user_id)->notify(new FriendApplicationCompletedNotification($r));
    }
}
