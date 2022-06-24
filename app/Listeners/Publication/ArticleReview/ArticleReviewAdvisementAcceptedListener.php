<?php

namespace App\Listeners\Publication\ArticleReview;

use App\Events\Publication\ArticleReview\ArticleReviewAdvisementAcceptedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Publication\ArticleReview\ArticleReviewAdvisementAcceptedNotification;
use App\Home\Publication\ArticleReview;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleReview\ArticleReviewEvent;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleReviewAdvisementAcceptedListener
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
     * @param  ArticleReviewAdvisementAcceptedEvent  $event
     * @return void
     */
    public function handle(ArticleReviewAdvisementAcceptedEvent $event)
    {
        //评审计划建议被小组接受后，通知原作者
        $advise = $event->articleReviewAdvise;
        $articleReview = ArticleReview::find($advise->rid);
        $article = Article::find($articleReview->aid);
        // 添加事件到用户动态
        $behavior = '接受评审计划建议：';
        $objectName = $advise->title;
        $objectURL = '/publication/review/'.$article->id.'/'.$article->title.'#reviewAdvise'.$advise->id;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($advise->recipient_id,$advise->recipient,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加热度记录
        $b_id = 31;
        ArticleTemperatureRecord::recordAdd($article->id,$advise->recipient_id,$b_id,$createtime);
        //积分和成长值+10
        User::expAndGrowValue($advise->recipient_id,'10','10');
        ArticleReviewEvent::reviewEventAdd($advise->rid,$advise->recipient_id,$advise->recipient,'接受了'.$advise->author.'的建议评论。');
        // 通知被回复作者
        User::find($advise->author_id)->notify(new ArticleReviewAdvisementAcceptedNotification($advise));
    }
}
