<?php

namespace App\Listeners\Publication\ArticleDiscussion;

use App\Events\Publication\ArticleDiscussion\ArticleOpponentAcceptedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Publication\ArticleDiscussion\ArticleOpponentAcceptedNotification;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleDiscussion\ArticleDiscussionEvent;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleOpponentAcceptedListener
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
     * @param  ArticleOpponentAcceptedEvent  $event
     * @return void
     */
    public function handle(ArticleOpponentAcceptedEvent $event)
    {
        // 反对的讨论被接受后，仅通知讨论的作者
        $opponent = $event->articleOpponent;
        $article = Article::find($opponent->aid);
        //反对被接受后，作者的积分和成长值+20
        User::expAndGrowValue($opponent->author_id,'20','20');
        //反对被接受后，操作者的积分和成长值+20
        User::expAndGrowValue($opponent->recipient_id,'20','20');
        // 添加讨论事件
        ArticleDiscussionEvent::discussionEventAdd($article->id,$opponent->recipient_id,$opponent->recipient,'接受了['.$opponent->author.']提出的反对意见。');
        // 添加事件到用户动态
        $behavior = '接受了著作反对讨论：';
        $objectName = $opponent->title;
        $objectURL = '/publication/discussion/'.$article->id.'/'.$article->title.'#discussionOpponent'.$opponent->id;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($opponent->recipient_id,$opponent->recipient,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加热度记录
        $b_id = 49;
        ArticleTemperatureRecord::recordAdd($opponent->aid,$opponent->recipient_id,$b_id,$createtime);
        // 给反对作者发送通知
        User::find($opponent->author_id)->notify(new ArticleOpponentAcceptedNotification($opponent));
    }
}
