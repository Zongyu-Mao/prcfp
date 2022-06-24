<?php

namespace App\Listeners\Personal\Relationship;

use App\Events\Personal\Relationship\FriendPrivateLetterSentEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Personal\Relationship\FriendPrivateLetterSentNotification;
use App\Models\User;

class FriendPrivateLetterSentListener
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
     * @param  FriendPrivateLetterSentEvent  $event
     * @return void
     */
    public function handle(FriendPrivateLetterSentEvent $event)
    {
        //私信发送后，仅需通知被私信用户有私信啦
        User::find($event->friendPrivateLetter->to_id)->notify(new FriendPrivateLetterSentNotification($event->friendPrivateLetter));
    }
}
