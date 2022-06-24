<?php

namespace App\Listeners\Management\Surveillance;

use App\Events\Management\Surveillance\MarkReactedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Committee\Surveillance\SurveillanceMark;

class MarkReactedListener
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
     * @param  MarkReactedEvent  $event
     * @return void
     */
    public function handle(MarkReactedEvent $event)
    {
        //  event 主要是判断一下是否需要更改status
        $re = $event->surveillanceMarkReactRecord;
        $user = Auth::user();
        $mark = SurveillanceMark::find($re->mark_id);
        if($re->stand==3) {
            if($user->id == $re->user_id) {
                $user->decrement('gold');
            }
            $mark->update(['status'=>3]);
        }

    }
}
