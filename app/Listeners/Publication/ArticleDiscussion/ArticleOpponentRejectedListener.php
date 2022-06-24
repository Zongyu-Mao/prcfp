<?php

namespace App\Listeners\Publication\ArticleDiscussion;

use App\Events\Publication\ArticleDiscussion\ArticleOpponentRejectedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Publication\ArticleDiscussion\ArticleOpponentRejectedToUserNotification;
use App\Notifications\Publication\ArticleDiscussion\ArticleOpponentRejectedNotification;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleDiscussion\ArticleDiscussionEvent;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleOpponentRejectedListener
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
     * @param  ArticleOpponentRejectedEvent  $event
     * @return void
     */
    public function handle(ArticleOpponentRejectedEvent $event)
    {
        // 反对的讨论被拒绝后，原则上应通知所有相关用户，由于拒绝评论是新建的，因此原讨论的接受者与作者身份互换
        $opponent = $event->articleOpponent;
        $article = Article::find($opponent->aid);
        $cooperation = ArticleCooperation::find($article->cooperation_id);
        //反对被拒绝后，操作者的积分和成长值+20
        User::expAndGrowValue($opponent->author_id,'100','100');
        // 添加讨论事件
        ArticleDiscussionEvent::discussionEventAdd($article->id,$opponent->author_id,$opponent->author,'拒绝了['.$opponent->recipient.']提出的反对意见。');
        // 添加事件到用户动态
        $behavior = '拒绝了著作反对讨论：';
        $objectName = $opponent->title;
        $objectURL = '/publication/discussion/'.$article->id.'/'.$article->title.'#discussionOpponent'.$opponent->id;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($opponent->author_id,$opponent->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加热度记录
        $b_id = 50;
        ArticleTemperatureRecord::recordAdd($opponent->aid,$opponent->author_id,$b_id,$createtime);
        // 给反对作者发送通知
        User::find($opponent->recipient_id)->notify(new ArticleOpponentRejectedToUserNotification($opponent));
        // 通知词条相关用户
        $manage_id = $article->manage_id;
        if(count($cooperation)){
            $crewArr = $cooperation->crews()->pluck('user_id')->toArray();
            $initiate_id = $cooperation->manage_id;
            array_push($crewArr, $manage_id);
            array_push($crewArr, $initiate_id); 
        }else{
            $crewArr = [];
            array_push($crewArr, $manage_id);
        }
        // 获取词条的关注用户
        $focusUsers = $article->ArticleFocus()->pluck('user_id')->toArray();
        // 合并协作组与兴趣用户
        $users = array_unique(array_merge($crewArr,$focusUsers));
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyArticleAdd($result));
        Notification::send($usersToNotification, new ArticleOpponentRejectedNotification($opponent));
    }
}
