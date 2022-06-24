<?php

namespace App\Listeners\Publication\ArticleReview;

use App\Events\Publication\ArticleReview\ArticleReviewTerminatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleReview\ArticleReviewOpponent;
use App\Home\Publication\ArticleReview\ArticleReviewAdvise;

class ArticleReviewTerminatedListener
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
     * @param  ArticleReviewTerminatedEvent  $event
     * @return void
     */
    public function handle(ArticleReviewTerminatedEvent $event)
    {
        $review = $event->entryReview;
        if($review->status==1) {
            // 这是强行终止的
            ArticleReviewOpponent::where('rid',$review->id)->update(['status'=>5]);
            ArticleReviewAdvise::where('rid',$review->id)->update(['status'=>5]);
        } else if($review->status==2) {
            // 这是基本正常结束的
        }
    }
}
