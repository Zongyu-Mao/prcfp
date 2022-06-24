<?php

namespace App\Listeners\Management\Role;

use App\Events\Management\Role\RoleJudgedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\Management\Committee\RoleJudgedNotification;
use App\Models\Management\Role\RoleJudgeRecord;
use App\Models\User;
use Carbon\Carbon;

class RoleJudgedListener
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
     * @param  RoleJudgedEvent  $event
     * @return void
     */
    public function handle(RoleJudgedEvent $event)
    {
        $record = $event->roleJudgeRecord;
        $user = User::find($record->user_id);
        $handle_id = $record->handle_id;
        
        
        $user->notify(new RoleJudgedNotification($record));
    }
}
