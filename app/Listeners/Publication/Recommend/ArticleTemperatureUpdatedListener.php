<?php

namespace App\Listeners\Publication\Recommend;

use App\Events\Publication\Recommend\ArticleTemperatureUpdatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Publication\Article;
use App\Home\Publication\Recommend\ArticleRecommendation;

class ArticleTemperatureUpdatedListener
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
     * @param  ArticleTemperatureUpdatedEvent  $event
     * @return void
     */
    public function handle(ArticleTemperatureUpdatedEvent $event)
    {
        //这里主要是确认推荐表是否需要更改，即对比本词条是否在推荐表中，如果不在其热度是否大于推荐表中该分类的词条热度
        $tem = $event->articleTemperature;
        $cid = Article::find($tem->aid)->cid;
        // 如果本分类没有记录，就直接把本条记录写入，当作初始化本分类推荐
        $recommend = ArticleRecommendation::where('cid',$cid)->count() ? ArticleRecommendation::where('cid',$cid)->first():ArticleRecommendation::recommendationAdd($cid,$tem->aid);
        if($tem->aid != $recommend->aid){
            // 推荐表与对比词条不同，因此比较
            // 本词条的热度
            $temperature = $tem->temperature;
            // 原推荐热度
            $old_tem = $recommend->temperature;
            if($temperature > $old_tem){
                ArticleRecommendation::recommendationUpdate($recommend->id,$tem->aid);
            }
        }
    }
}
