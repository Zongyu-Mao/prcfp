<?php

namespace App\Listeners\Publication\ArticleCooperation;

use App\Events\Publication\ArticleCooperation\ArticleCooperationDiscussionCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\ArticleCooperation\ArticleCooperationEvent;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Models\User;

class ArticleCooperationDiscussionCreatedListener
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
     * @param  ArticleCooperationDiscussionCreatedEvent  $event
     * @return void
     */
    public function handle(ArticleCooperationDiscussionCreatedEvent $event)
    {
        //协作计划讨论是内部事宜，仅需写入协作事件
        // 添加协作事件
        $discussion = $event->articleCooperationDiscussion;
        $article = Article::find(ArticleCooperation::find($discussion->cooperation_id)->aid);
        ArticleCooperationEvent::cooperationEventAdd($discussion->cooperation_id,$discussion->author_id,$discussion->author,'发表了协作计划讨论。');
        User::expAndGrowValue($discussion->author_id,5,5);
        // 词条添加热度记录
        $b_id = 19;
        ArticleTemperatureRecord::recordAdd($article->id,$discussion->author_id,$b_id,$discussion->created_at);
    }
}
