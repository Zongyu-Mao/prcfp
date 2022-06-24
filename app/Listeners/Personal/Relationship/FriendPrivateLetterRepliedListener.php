<?php

namespace App\Listeners\Personal\Relationship;

use App\Events\Personal\Relationship\FriendPrivateLetterRepliedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Personal\Relationship\FriendPrivateLetterRepliedNotification;
use App\Home\Personal\Relationship\FriendPrivateLetter;
use App\Models\User;

class FriendPrivateLetterRepliedListener
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
     * @param  FriendPrivateLetterRepliedEvent  $event
     * @return void
     */
    public function handle(FriendPrivateLetterRepliedEvent $event)
    {
         //私信回复后，仅需通知发送者被回复
        User::find($event->friendPrivateLetter->to_id)->notify(new FriendPrivateLetterRepliedNotification($event->friendPrivateLetter));
        // 更改被回复私信状态为‘被回复’
        FriendPrivateLetter::where('id',$event->friendPrivateLetter->pid)->update([
            'status' => '1'
            ]);
    }
}
