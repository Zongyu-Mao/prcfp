<?php

namespace App\Listeners\Publication\ArticleDiscussion;

use App\Events\Publication\ArticleDiscussion\ArticleAdvisementRejectedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Publication\ArticleDiscussion\ArticleAdvisementRejectedToUserNotification;
use App\Notifications\Publication\ArticleDiscussion\ArticleAdvisementRejectedNotification;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleDiscussion\ArticleDiscussionEvent;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleAdvisementRejectedListener
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
     * @param  ArticleAdvisementRejectedEvent  $event
     * @return void
     */
    public function handle(ArticleAdvisementRejectedEvent $event)
    {
        // 词条建议的讨论被拒绝后，通知原建议作者及协作小组
        $advise = $event->articleAdvise;
        $article = Article::find($advise->aid);
        $cooperation = ArticleCooperation::find($article->cooperation_id);
        // 添加事件到用户动态
        $behavior = '拒绝了著作建议讨论：';
        $objectName = $advise->title;
        $objectURL = '/publication/discussion/'.$article->id.'/'.$article->title.'#discussionAdvise'.$advise->id;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($advise->author_id,$advise->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        //反对被拒绝后，操作者的积分和成长值+20
        User::expAndGrowValue($advise->author_id,'100','100');
        // 添加讨论事件
        ArticleDiscussionEvent::discussionEventAdd($article->id,$advise->author_id,$advise->author,'拒绝了['.$advise->recipient.']提出的反对意见。');
        // 添加热度记录
        $b_id = 53;
        ArticleTemperatureRecord::recordAdd($advise->aid,$advise->author_id,$b_id,$createtime);
        // 给反对作者发送通知
        User::find($advise->recipient_id)->notify(new ArticleAdvisementRejectedToUserNotification($advise));
        // 开启对协作组成员的通知
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
        // $focusUsers = $article->ArticleFocus()->pluck('user_id')->toArray();
        // 合并协作组与兴趣用户
        // $users = array_unique(array_merge($crewArr,$focusUsers));
        $users = array_unique($crewArr);
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyArticleAdd($result));
        Notification::send($usersToNotification, new ArticleAdvisementRejectedNotification($advise));
    }
}
