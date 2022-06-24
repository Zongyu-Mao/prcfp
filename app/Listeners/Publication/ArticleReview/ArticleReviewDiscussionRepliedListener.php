<?php

namespace App\Listeners\Publication\ArticleReview;

use App\Events\Publication\ArticleReview\ArticleReviewDiscussionRepliedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Publication\ArticleReview\ArticleReviewDiscussionRepliedNotification;
use App\Home\Publication\ArticleReview;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleReview\ArticleReviewEvent;
use App\Home\Publication\ArticleReview\ArticleReviewDiscussion;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleReviewDiscussionRepliedListener
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
     * @param  ArticleReviewDiscussionRepliedEvent  $event
     * @return void
     */
    public function handle(ArticleReviewDiscussionRepliedEvent $event)
    {
        //评论回复仅需通知被回复评论作者即可
        $discussion = $event->articleReviewDiscussion;
        $articleReview = ArticleReview::find($discussion->rid);
        $article = Article::find($articleReview->aid);
        $parentDiscussion = ArticleReviewDiscussion::find($discussion->pid);
        // 添加事件到用户动态
        $behavior = '回复了评审计划支持/中立意见：';
        $objectName = $articleReview->title;
        $objectURL = '/publication/review/'.$article->id.'/'.$article->title.'#reviewDiscussion'.$discussion->id;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($discussion->author_id,$discussion->getAuthor->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        //发表了有效的评审意见后，积分和成长值+50
        User::expAndGrowValue($discussion->author_id,'10','10');
        ArticleReviewEvent::reviewEventAdd($discussion->rid,$discussion->author_id,$discussion->getAuthor->username,'回复了'.$parentDiscussion->getAuthor->username.'的评论。');
        // 添加热度记录
        $b_id = 28;
        ArticleTemperatureRecord::recordAdd($article->id,$discussion->author_id,$b_id,$createtime);
        // 通知被回复作者
        User::find($parentDiscussion->author_id)->notify(new ArticleReviewDiscussionRepliedNotification($discussion));
    }
}
