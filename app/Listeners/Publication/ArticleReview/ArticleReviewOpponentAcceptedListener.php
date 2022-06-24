<?php

namespace App\Listeners\Publication\ArticleReview;

use App\Events\Publication\ArticleReview\ArticleReviewOpponentAcceptedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Publication\ArticleReview\ArticleReviewOpponentAcceptedToUserNotification;
use App\Notifications\Publication\ArticleReview\ArticleReviewOpponentAcceptedNotification;
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

class ArticleReviewOpponentAcceptedListener
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
     * @param  ArticleReviewOpponentAcceptedEvent  $event
     * @return void
     */
    public function handle(ArticleReviewOpponentAcceptedEvent $event)
    {
        //反对意见的接受，应通知协作组成员和原作者
        $opponent = $event->articleReviewOpponent;
        $articleReview = ArticleReview::find($opponent->rid);
        $article = Article::find($articleReview->aid);
        $cooperation = ArticleCooperation::find($article->cooperation_id);
        // 添加到用户动态
        $behavior = '接受了评审计划反对意见：';
        $objectName = $opponent->title;
        $objectURL = '/publication/review/'.$article->id.'/'.$article->title.'#reviewOpponent'.$opponent->id;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        UserDynamic::dynamicAdd($opponent->recipient_id,$opponent->recipient,$behavior,$objectName,$objectURL,$fromName,$fromURL,Carbon::now());
        //发表了有效的讨论后，积分和成长值+100
        User::expAndGrowValue($opponent->author_id,'100','100');
        // 添加讨论事件
        ArticleReviewEvent::reviewEventAdd($articleReview->id,$opponent->recipient_id,$opponent->recipient,'接受了['.$opponent->author.']提出的反对意见：<'.$opponent->title.'>。');
        // 通知原反对作者被拒绝
        User::find($opponent->author_id)->notify(new ArticleReviewOpponentAcceptedToUserNotification($opponent));
        // 添加热度记录
        $b_id = 26;
        ArticleTemperatureRecord::recordAdd($article->id,$opponent->author_id,$b_id,$createtime);
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
        // 合并协作组与兴趣用户
        $users = array_unique($crewArr);
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyArticleAdd($result));
        Notification::send($usersToNotification, new ArticleReviewOpponentAcceptedNotification($opponent));
    }
}
