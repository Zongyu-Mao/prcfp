<?php

namespace App\Listeners\Publication\ArticleReview;

use App\Events\Publication\ArticleReview\ArticleReviewAdvisementRejectedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Publication\ArticleReview\ArticleReviewAdvisementRejectedToUserNotification;
use App\Notifications\Publication\ArticleReview\ArticleReviewAdvisementRejectedNotification;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\ArticleReview;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleReview\ArticleReviewAdvise;
use App\Home\Publication\ArticleReview\ArticleReviewEvent;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleReviewAdvisementRejectedListener
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
     * @param  ArticleReviewAdvisementRejectedEvent  $event
     * @return void
     */
    public function handle(ArticleReviewAdvisementRejectedEvent $event)
    {
        //评审计划建议的拒绝，应通知协作小组成员和原作者
        $advise = $event->articleReviewAdvise;
        $articleReview = ArticleReview::find($advise->rid);
        $parentReviewAdvise = ArticleReviewAdvise::find($advise->pid);
        $article = Article::find($articleReview->aid);
        $cooperation = ArticleCooperation::find($article->cooperation_id);
        // 添加事件到用户动态
        $behavior = '拒绝评审计划建议：';
        $objectName = $advise->title;
        $objectURL = '/publication/review/'.$article->id.'/'.$article->title.'#reviewAdvise'.$advise->id;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($advise->author_id,$advise->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        //发表了有效的讨论后，积分和成长值+100
        User::expAndGrowValue($advise->author_id,'100','100');
        // 添加讨论事件
        ArticleReviewEvent::reviewEventAdd($articleReview->id,$advise->author_id,$advise->author,'拒绝了['.$advise->recipient.']提出的建议：<'.$parentReviewAdvise->title.'>，理由：<'.$advise->title.'>。');
        // 添加热度记录
        $b_id = 30;
        ArticleTemperatureRecord::recordAdd($article->id,$advise->recipient_id,$b_id,$createtime);
        // 通知原反对作者被拒绝
        User::find($advise->recipient_id)->notify(new ArticleReviewAdvisementRejectedToUserNotification($advise));
        // 开启对协作组成员的通知
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
        // 合并协作组
        $users = array_unique($crewArr);
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyArticleAdd($result));
        Notification::send($usersToNotification, new ArticleReviewAdvisementRejectedNotification($advise));
    }
}
