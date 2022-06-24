<?php

namespace App\Listeners\Personal\Relationship;

use App\Events\Personal\Relationship\UserFriendApplicationCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Personal\Relationship\FriendApplicationCreatedNotification;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class UserFriendApplicationCreatedListener
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
     * @param  UserFriendApplicationCreatedEvent  $event
     * @return void
     */
    public function handle(UserFriendApplicationCreatedEvent $event)
    {
        //对方申请添加好友，通知用户该申请
        $user_id = $event->userFriendApplicationRecord->user_id;
        // $username = $event->userFriendApplicationRecord->username;
        // $application_id = $event->userFriendApplicationRecord->application_id;
        // $application_username = $event->userFriendApplicationRecord->application_username;
        // // 添加事件到用户动态
        // $behavior = '申请好友添加：';
        // $objectName = $username;
        // $objectURL = '/home/personalHomepage/'.$user_id;
        // $fromName = '用户：'.$application_username;
        // $fromURL = '/home/PersonalHomepage/'.$application_id;
        // $createtime = Carbon::now();
        // UserDynamic::dynamicAdd($application_id,$application_username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 给被申请用户发送通知
        User::find($user_id)->notify(new FriendApplicationCreatedNotification($event->userFriendApplicationRecord));
    }
}
