<?php

namespace App\Listeners\Examination\ExamReview;

use App\Events\Examination\ExamReview\ExamReviewTerminatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamReview\ExamReviewOpponent;
use App\Home\Examination\ExamReview\ExamReviewAdvise;

class ExamReviewTerminatedListener
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
     * @param  ExamReviewTerminatedEvent  $event
     * @return void
     */
    public function handle(ExamReviewTerminatedEvent $event)
    {
        $review = $event->entryReview;
        if($review->status==1) {
            // 这是强行终止的
            ExamReviewOpponent::where('rid',$review->id)->update(['status'=>5]);
            ExamReviewAdvise::where('rid',$review->id)->update(['status'=>5]);
        } else if($review->status==2) {
            // 这是基本正常结束的
        }
    }
}
