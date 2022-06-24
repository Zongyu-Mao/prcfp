<?php

namespace App\Listeners\Personal\PrivateMedal;

use App\Events\Personal\PrivateMedal\PrivateMedalGivenEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\Personal\PrivateMedal\PrivateMedalGivenNotification;
use App\Models\User;

class PrivateMedalGivenListener
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
     * @param  PrivateMedalGivenEvent  $event
     * @return void
     */
    public function handle(PrivateMedalGivenEvent $event)
    {
        // 目前赠送后只需要通知被赠送人
        $medal = $event->privateMedal;
        User::find($medal->owner_id)->notify(new PrivateMedalGivenNotification($medal));;
    }
}
