<?php

namespace App\Listeners\Publication\Article\Focus;

use App\Events\Publication\Article\Focus\ArticleCollectCanceledEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ArticleCollectCanceledListener
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
     * @param  ArticleCollectCanceledEvent  $event
     * @return void
     */
    public function handle(ArticleCollectCanceledEvent $event)
    {
        //取消收藏后的热度更新
        $focus = $event->articleCollectUser;
        // 添加热度记录
        $b_id = 7;

        ArticleTemperatureRecord::recordAdd($focus->article_id,auth('api')->user()->id,$b_id,Carbon::now());
    }
}
