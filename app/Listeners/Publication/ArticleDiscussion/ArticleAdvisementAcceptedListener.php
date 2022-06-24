<?php

namespace App\Listeners\Publication\ArticleDiscussion;

use App\Events\Publication\ArticleDiscussion\ArticleAdvisementAcceptedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Publication\ArticleDiscussion\ArticleAdvisementAcceptedNotification;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleDiscussion\ArticleDiscussionEvent;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleAdvisementAcceptedListener
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
     * @param  ArticleAdvisementAcceptedEvent  $event
     * @return void
     */
    public function handle(ArticleAdvisementAcceptedEvent $event)
    {
        // 词条反对的讨论被接受后，仅通知讨论的作者
        $advise = $event->articleAdvise;
        $article = Article::find($advise->aid);
        // 添加事件到用户动态
        $behavior = '接受了著作建议讨论：';
        $objectName = $advise->title;
        $objectURL = '/publication/discussion/'.$article->id.'/'.$article->title.'#discussionAdvise'.$advise->id;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($advise->recipient_id,$advise->recipient,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        //建议被接受后，作者的积分和成长值+20
        User::expAndGrowValue($advise->author_id,'20','20');
        //建议被接受后，操作者的积分和成长值+20
        User::expAndGrowValue($advise->recipient_id,'20','20');
        // 添加讨论事件
        ArticleDiscussionEvent::discussionEventAdd($article->id,$advise->recipient_id,$advise->recipient,'接受了['.$advise->author.']提出的建议。');
        // 添加热度记录
        $b_id = 52;
        ArticleTemperatureRecord::recordAdd($advise->aid,$advise->recipient_id,$b_id,$createtime);
        // 给反对作者发送通知
        User::find($advise->author_id)->notify(new ArticleAdvisementAcceptedNotification($advise));
    }
}
