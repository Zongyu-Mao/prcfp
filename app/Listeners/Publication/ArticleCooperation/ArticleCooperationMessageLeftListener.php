<?php

namespace App\Listeners\Publication\ArticleCooperation;

use App\Events\Publication\ArticleCooperation\ArticleCooperationMessageLeftEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\ArticleCooperation\ArticleCooperationEvent;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use Carbon\Carbon;
use App\Models\User;

class ArticleCooperationMessageLeftListener
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
     * @param  ArticleCooperationMessageLeftEvent  $event
     * @return void
     */
    public function handle(ArticleCooperationMessageLeftEvent $event)
    {
        //用户在协作计划的留言，目前只需要写入协作事件中
        // 添加协作事件
        $msg = $event->articleCooperationMessage;
        $aid = ArticleCooperation::find($msg->cooperation_id)->aid;
        $createtime = Carbon::now();
        User::expAndGrowValue($msg->author_id,'10','10');
        ArticleCooperationEvent::cooperationEventAdd($msg->cooperation_id,$msg->author_id,$msg->author,'在协作计划发表了讨论留言:'.$msg->title.'。');
        // 词条添加热度记录
        $b_id = 20;
        ArticleTemperatureRecord::recordAdd($aid,$msg->author_id,$b_id,$createtime);
    }
}
