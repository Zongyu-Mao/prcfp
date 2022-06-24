<?php

namespace App\Listeners\Publication\Recommend;

use App\Events\Publication\Recommend\ArticleTemperatureRecordAddedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Publication\Article;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Publication\Recommend\ArticleTemperature;
use App\Home\Personnel\Behavior;
use Illuminate\Support\Facades\Redis;

class ArticleTemperatureRecordAddedListener
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
     * @param  ArticleTemperatureRecordAddedEvent  $event
     * @return void
     */
    public function handle(ArticleTemperatureRecordAddedEvent $event)
    {
        // 记录写入成功后，更新内容的热度
        $record = $event->articleTemperatureRecord;
        $aid = $record->aid;
        $a = Article::find($aid);
        $cid = $a->cid;
        $tem = ArticleTemperature::where('aid',$record->aid)->first();
        // 这里更新返回的是更新后的模型
        $tem = $tem ?  $tem : ArticleTemperature::recordInitialization($aid);
        $behavior = Behavior::find($record->behavior_id);
        // 变更热度热度
        Redis::INCRBY('article:temperature:'.$aid,$behavior->score);
        Redis::ZINCRBY('article:temperature:rank',$behavior->score,$aid); //百科总热度榜
        if($a->level>=4) {
            Redis::ZINCRBY('article:featured:temperature:rank',$behavior->score,$aid);
            Redis::ZINCRBY('article:featured:classification:temperature:rank:'.$cid,$behavior->score,$aid);
            if($a->level==5) {
                Redis::ZINCRBY('article:pr:temperature:rank',$behavior->score,$aid);
                Redis::ZINCRBY('article:pr:classification:temperature:rank:'.$cid,$behavior->score,$aid);
            }
        }
        Redis::ZINCRBY('classification:temperature:rank',$behavior->score,$cid);//分类总热度榜
        Redis::ZINCRBY('article:classification:temperature:rank:'.$cid,$behavior->score,$aid);//内容带分类热度榜
        // $score = $tem->temperature + $behavior->score;
        // // 更新热度
        // ArticleTemperature::recommendationUpdate($tem->id,$score);
    }
}
