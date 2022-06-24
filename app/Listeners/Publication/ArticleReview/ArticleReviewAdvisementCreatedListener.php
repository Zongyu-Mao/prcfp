<?php

namespace App\Listeners\Publication\ArticleReview;

use App\Events\Publication\ArticleReview\ArticleReviewAdvisementCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Publication\ArticleReview\ArticleReviewAdvisementCreatedNotification;
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

class ArticleReviewAdvisementCreatedListener
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
     * @param  ArticleReviewAdvisementCreatedEvent  $event
     * @return void
     */
    public function handle(ArticleReviewAdvisementCreatedEvent $event)
    {
        //评审计划建议意见的创建，应通知协作小组成员
        // $ArticleReview = ArticleReview::find($advise->rid);
        $advise = $event->articleReviewAdvise;
        $article = Article::find(ArticleReview::find($advise->rid)->aid);
        $cooperation = ArticleCooperation::find($article->cooperation_id);
        // 添加事件到用户动态
        $behavior = '发表评审计划建议：';
        $objectName = $advise->title;
        $objectURL = '/publication/review/'.$article->id.'/'.$article->title.'#reviewAdvise'.$advise->id;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($advise->author_id,$advise->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $articleBehavior = '新增评审计划建议';
        ArticleDynamic::dynamicAdd($article->id,$article->title,$articleBehavior,$objectName,$objectURL,$createtime);
        //发表了有效的讨论后，积分和成长值+80
        User::expAndGrowValue($advise->author_id,'80','80');
        // 添加讨论事件
        ArticleReviewEvent::reviewEventAdd($advise->rid,$advise->author_id,$advise->author,'提出了建议内容《'.$advise->title.'》。');
        // 添加热度记录
        $b_id = 29;
        ArticleTemperatureRecord::recordAdd($article->id,$advise->author_id,$b_id,$createtime);
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
        // $focusUsers = $Article->ArticleFocus()->pluck('user_id')->toArray();
        // 合并协作组与兴趣用户
        $users = array_unique($crewArr);
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyArticleAdd($result));
        Notification::send($usersToNotification, new ArticleReviewAdvisementCreatedNotification($advise));
    }
}
