<?php

namespace App\Listeners\Publication\ArticleDiscussion;

use App\Events\Publication\ArticleDiscussion\ArticleOpponentCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Publication\ArticleDiscussion\ArticleOpponentCreatedNotification;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\ArticleReview;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleDiscussion\ArticleDiscussionEvent;
use App\Home\Publication\Article\ArticleDynamic;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleOpponentCreatedListener
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
     * @param  ArticleOpponentCreatedEvent  $event
     * @return void
     */
    public function handle(ArticleOpponentCreatedEvent $event)
    {
        //反对的讨论创建后，仅通知协作组成员和关注词条用户，不必通知关注该分类的用户
        $opponent = $event->articleOpponent;
        $article = Article::find($opponent->aid);
        $cooperation = ArticleCooperation::find($article->cooperation_id);
        // 添加事件到用户动态
        $behavior = '发表了著作反对讨论：';
        $objectName = $opponent->title;
        $objectURL = '/publication/discussion/'.$article->id.'/'.$article->title.'#discussionOpponent'.$opponent->id;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($opponent->author_id,$opponent->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $articleBehavior = '新增反对讨论';
        ArticleDynamic::dynamicAdd($article->id,$article->title,$articleBehavior,$objectName,$objectURL,$createtime);
        //发表了有效的讨论后，积分和成长值+100
        User::expAndGrowValue($opponent->author_id,'100','100');
        // 添加讨论事件
        ArticleDiscussionEvent::discussionEventAdd($article->id,$opponent->author_id,$opponent->author,'发表了立场为[反对]的讨论内容：<'.$opponent->title.'>。');
        // 添加热度记录
        $b_id = 48;
        ArticleTemperatureRecord::recordAdd($opponent->aid,$opponent->author_id,$b_id,$createtime);
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
        Notification::send($usersToNotification, new ArticleOpponentCreatedNotification($opponent));
    }
}
