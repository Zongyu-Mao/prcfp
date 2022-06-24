<?php

namespace App\Listeners\Personal\Relationship;

use App\Events\Personal\Relationship\UserFriendApplicationRejectedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Personal\Relationship\FriendApplicationRejectedNotification;
use Carbon\Carbon;
use App\Models\User;

class UserFriendApplicationRejectedListener
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
     * @param  UserFriendApplicationRejectedEvent  $event
     * @return void
     */
    public function handle(UserFriendApplicationRejectedEvent $event)
    {
        //用户拒绝了该申请，仅需通知申请用户即可
        $application_id = $event->userFriendApplicationRecord->application_id;
        // 给被申请用户发送通知
        User::find($application_id)->notify(new FriendApplicationRejectedNotification($event->userFriendApplicationRecord));
    }
}
