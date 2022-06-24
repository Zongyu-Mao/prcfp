<?php

namespace App\Listeners\Publication\Recommend;

use App\Events\Publication\Recommend\ArticleRecommendationUpdatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Publication\Recommend\ArticleRecommendRecord;

class ArticleRecommendationUpdatedListener
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
     * @param  ArticleRecommendationUpdatedEvent  $event
     * @return void
     */
    public function handle(ArticleRecommendationUpdatedEvent $event)
    {
        //更新推荐记录
        $rec = $event->articleRecommendation;
        $createtime = $rec->updated_at;
        ArticleRecommendRecord::recordAdd($rec->cid,$rec->aid,$createtime);
    }
}
