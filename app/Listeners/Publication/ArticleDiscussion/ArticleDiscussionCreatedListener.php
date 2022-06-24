<?php

namespace App\Listeners\Publication\ArticleDiscussion;

use App\Events\Publication\ArticleDiscussion\ArticleDiscussionCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Publication\ArticleDiscussion\ArticleDiscussionCreatedNotification;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleDiscussion\ArticleDiscussionEvent;
use App\Home\Publication\Article\ArticleDynamic;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleDiscussionCreatedListener
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
     * @param  ArticleDiscussionCreatedEvent  $event
     * @return void
     */
    public function handle(ArticleDiscussionCreatedEvent $event)
    {
        //词条普通讨论创建后，仅通知协作组成员，不必通知其余用户
        $discussion = $event->articleDiscussion;
        $article = Article::find($discussion->aid);
        $cooperation = ArticleCooperation::find($article->cooperation_id);
        // 添加事件到用户动态
        $behavior = '发表著作普通讨论：';
        $objectName = $discussion->title;
        $objectURL = '/publication/discussion/'.$article->id.'/'.$article->title.'#discussion'.$discussion->id;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($discussion->author_id,$discussion->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $articleBehavior = '新增普通讨论';
        ArticleDynamic::dynamicAdd($article->id,$article->title,$articleBehavior,$objectName,$objectURL,$createtime);
        //发表了有效的讨论后，积分和成长值+20
        User::expAndGrowValue($discussion->author_id,'20','20');
        // 添加讨论事件
        ArticleDiscussionEvent::discussionEventAdd($article->id,$discussion->author_id,$discussion->author,'发表了[普通]讨论内容：<'.$discussion->title.'>。');
        // 添加热度记录
        $b_id = 54;
        ArticleTemperatureRecord::recordAdd($discussion->aid,$discussion->author_id,$b_id,$createtime);
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
        Notification::send($usersToNotification, new ArticleDiscussionCreatedNotification($discussion));
    }
}
