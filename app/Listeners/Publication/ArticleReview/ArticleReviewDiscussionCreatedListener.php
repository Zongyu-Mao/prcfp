<?php

namespace App\Listeners\Publication\ArticleReview;

use App\Events\Publication\ArticleReview\ArticleReviewDiscussionCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Publication\ArticleReview\ArticleReviewDiscussionCreatedNotification;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\ArticleReview;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleReview\ArticleReviewEvent;
use App\Home\Publication\Article\ArticleDynamic;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleReviewDiscussionCreatedListener
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
     * @param  ArticleReviewDiscussionCreatedEvent  $event
     * @return void
     */
    public function handle(ArticleReviewDiscussionCreatedEvent $event)
    {
        //这里触发评审中立及支持事件，另外也触发评审的普通回复事件
        //中立票比较简单，投票成功后，通知相关用户即可
        $discussion = $event->articleReviewDiscussion;
        $articleReview = ArticleReview::find($discussion->rid);
        $article = Article::find($articleReview->aid);
        $cooperation = ArticleCooperation::find($article->cooperation_id);
        //发表了有效的评审意见后，积分和成长值+50
        User::expAndGrowValue($discussion->author_id,'50','50');
        // 添加评审事件
        if($discussion->standpoint == '1'){
            ArticleReviewEvent::reviewEventAdd($discussion->rid,$discussion->author_id,$discussion->getAuthor->username,'支持本次评审计划通过。');
            $behavior = '发表评审计划支持意见：';
            $articleBehavior = '新增评审计划支持意见';
        }elseif($discussion->standpoint == '3'){
            ArticleReviewEvent::reviewEventAdd($discussion->rid,$discussion->author_id,$discussion->getAuthor->username,'投票并保持中立立场。');
            $behavior = '发表评审计划中立意见：';
            $articleBehavior = '新增评审计划中立意见';
        }

        // 添加事件到用户动态
        $objectName = $articleReview->title;
        $objectURL = '/publication/review/'.$article->id.'/'.$article->title.'#reviewDiscussion'.$discussion->id;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($discussion->author_id,$discussion->getAuthor->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        ArticleDynamic::dynamicAdd($article->id,$article->title,$articleBehavior,$objectName,$objectURL,$createtime);
        // 添加热度记录
        $b_id = 27;
        ArticleTemperatureRecord::recordAdd($article->id,$discussion->author_id,$b_id,$createtime);
        // 开启对协作组成员和关注词条用户的通知
        $manage_id = $article->manage_id;
        if($cooperation){
            $crewArr = $cooperation->crews()->pluck('user_id')->toArray();
            $initiate_id = $cooperation->manage_id;
            array_push($crewArr, $manage_id);
            array_push($crewArr, $initiate_id); 
        }else{
            $crewArr = [];
            array_push($crewArr, $manage_id);
        }
        // 获取词条的关注用户
        // $focusUsers = $article->ArticleFocus()->pluck('user_id')->toArray();
        // 合并协作组与兴趣用户
        $users = array_unique($crewArr);
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyArticleAdd($result));
        Notification::send($usersToNotification, new ArticleReviewDiscussionCreatedNotification($discussion));
    }
}
