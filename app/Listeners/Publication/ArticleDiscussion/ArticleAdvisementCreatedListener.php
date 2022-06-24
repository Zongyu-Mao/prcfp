<?php

namespace App\Listeners\Publication\ArticleDiscussion;

use App\Events\Publication\ArticleDiscussion\ArticleAdvisementCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Publication\ArticleDiscussion\ArticleAdvisementCreatedNotification;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleDiscussion\ArticleDiscussionEvent;
use App\Home\Publication\Article\ArticleDynamic;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleAdvisementCreatedListener
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
     * @param  ArticleAdvisementCreatedEvent  $event
     * @return void
     */
    public function handle(ArticleAdvisementCreatedEvent $event)
    {
        //提出建议后，仍然通知协作成员
        $advise = $event->articleAdvise;
        $article = Article::find($advise->aid);
        $cooperation = ArticleCooperation::find($article->cooperation_id);
        // 添加事件到用户动态
        $behavior = '发表了著作建议讨论：';
        $objectName = $advise->title;
        $objectURL = '/publication/discussion/'.$article->id.'/'.$article->title.'#discussionAdvise'.$advise->id;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($advise->author_id,$advise->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $ArticleBehavior = '新增建议讨论';
        ArticleDynamic::dynamicAdd($article->id,$article->title,$ArticleBehavior,$objectName,$objectURL,$createtime);
        //发表了有效的讨论后，积分和成长值+100
        User::expAndGrowValue($advise->author_id,'50','50');
        // 添加讨论事件
        ArticleDiscussionEvent::discussionEventAdd($article->id,$advise->author_id,$advise->author,'发表了立场为[建议]的讨论内容：<'.$advise->title.'>。');
        // 添加热度记录
        $b_id = 51;
        ArticleTemperatureRecord::recordAdd($advise->aid,$advise->author_id,$b_id,$createtime);
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
        // $users = array_unique(array_merge($crewArr,$focusUsers));
        $users = array_unique($crewArr);
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyArticleAdd($result));
        Notification::send($usersToNotification, new ArticleAdvisementCreatedNotification($advise));
    }
}
