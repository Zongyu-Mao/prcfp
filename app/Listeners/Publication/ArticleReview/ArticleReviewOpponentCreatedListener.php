<?php

namespace App\Listeners\Publication\ArticleReview;

use App\Events\Publication\ArticleReview\ArticleReviewOpponentCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Publication\ArticleReview\ArticleReviewOpponentCreatedNotification;
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

class ArticleReviewOpponentCreatedListener
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
     * @param  ArticleReviewOpponentCreatedEvent  $event
     * @return void
     */
    public function handle(ArticleReviewOpponentCreatedEvent $event)
    {
        //评审计划反对意见的创建，应通知协作小组成员和关注本词条用户
        $opponent = $event->articleReviewOpponent;
        $articleReview = ArticleReview::find($opponent->rid);
        $article = Article::find($articleReview->aid);
        $cooperation = ArticleCooperation::find($article->cooperation_id);
        // 添加事件到用户动态
        $behavior = '发表评审计划反对意见：';
        $objectName = $opponent->title;
        $objectURL = '/publication/review/'.$article->id.'/'.$article->title.'#reviewOpponent'.$opponent->id;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($opponent->author_id,$opponent->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $ArticleBehavior = '新增评审计划反对意见';
        ArticleDynamic::dynamicAdd($article->id,$article->title,$ArticleBehavior,$objectName,$objectURL,$createtime);
        //发表了有效的讨论后，积分和成长值+100
        User::expAndGrowValue($opponent->author_id,'100','100');
        // 添加讨论事件
        ArticleReviewEvent::reviewEventAdd($articleReview->id,$opponent->author_id,$opponent->author,'发表了立场为[反对]的评审意见：<'.$opponent->title.'>。');
        // 添加热度记录
        $b_id = 25;
        ArticleTemperatureRecord::recordAdd($article->id,$opponent->author_id,$b_id,$createtime);
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
        $focusUsers = $article->articleFocus()->pluck('user_id')->toArray();
        // 合并协作组与兴趣用户
        $users = array_unique(array_merge($crewArr,$focusUsers));
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyArticleAdd($result));
        Notification::send($usersToNotification, new ArticleReviewOpponentCreatedNotification($opponent));
    }
}
