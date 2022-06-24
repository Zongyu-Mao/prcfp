<?php

namespace App\Listeners\Publication\Article\Focus;

use App\Events\Publication\Article\Focus\ArticleFocusCanceledEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class ArticleFocusCanceledListener
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
     * @param  ArticleFocusCanceledEvent  $event
     * @return void
     */
    public function handle(ArticleFocusCanceledEvent $event)
    {
        //取消关注后的热度更新
        $focus = $event->articleFocusUser;
        // 添加热度记录
        $b_id = 5;

        ArticleTemperatureRecord::recordAdd($focus->article_id,auth('api')->user()->id,$b_id,Carbon::now());
    }
}
