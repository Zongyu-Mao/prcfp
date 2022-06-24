<?php

namespace App\Listeners\Encyclopedia\EntryReview;

use App\Events\Encyclopedia\EntryReview\EntryReviewTerminatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryReview\EntryReviewOpponent;
use App\Home\Encyclopedia\EntryReview\EntryReviewAdvise;

class EntryReviewTerminatedListener
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
     * @param  EntryReviewTerminatedEvent  $event
     * @return void
     */
    public function handle(EntryReviewTerminatedEvent $event)
    {
        // 评审结束，首先要将主内容的review_id更改为0
        $review = $event->entryReview;
        if($review->status==1) {
            // 这是强行终止的，需要更改所有评审内容的状态
            EntryReviewOpponent::where('rid',$review->id)->update(['status'=>5]);
            EntryReviewAdvise::where('rid',$review->id)->update(['status'=>5]);
        } else if($review->status==2) {
            // 这是基本正常结束的
        }

    }
}
