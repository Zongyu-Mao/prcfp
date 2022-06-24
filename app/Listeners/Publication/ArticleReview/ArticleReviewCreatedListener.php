<?php

namespace App\Listeners\Publication\ArticleReview;

use App\Events\Publication\ArticleReview\ArticleReviewCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Publication\ArticleReview\ArticleReviewCreatedNotification;
use App\Home\Publication\ArticleReview\ArticleReviewEvent;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\Article;
use App\Home\Announcement;
use App\Home\Classification;
use App\Home\Publication\Article\ArticleDynamic;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleReviewCreatedListener
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
     * @param  ArticleReviewCreatedEvent  $event
     * @return void
     */
    public function handle(ArticleReviewCreatedEvent $event)
    {
        //评审计划创建后，通知协作组成员和词条关注用户评审计划创建成功
        $review = $event->articleReview;
        $article = Article::find($review->aid);
        $cooperation = ArticleCooperation::find($article->cooperation_id);
        // 添加事件到用户动态
        $behavior = '开启评审计划：';
        $objectName = $review->title;
        $objectURL = '/publication/review/'.$article->id.'/'.$article->title;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($review->initiate_id,$review->initiater,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $articleBehavior = '开启评审计划';
        ArticleDynamic::dynamicAdd($article->id,$article->title,$articleBehavior,$objectName,$objectURL,$createtime);
        // 发布公告，2代表著作，2代表评审计划
        Announcement::announcementAdd('2','2','著作《'.$article->title.'》的评审计划<'.$review->title.'>已经创建。','/publication/review/'.$article->id.'/'.$article->title,$review->created_at);
        Article::where('id',$review->aid)->update(['review_id' => $review->id]);
        // 增加用户积分
        User::expAndGrowValue($review->initiate_id,'100','100');
        // 添加评审事件和词条事件
        ArticleReviewEvent::reviewEventAdd($review->id,$review->initiate_id,$review->initiater,'开启了评审计划<'.$review->title.'>。');
        // 添加热度记录
        $b_id = 24;
        ArticleTemperatureRecord::recordAdd($review->aid,$review->initiate_id,$b_id,$createtime);
        // 获取词条母专业兴趣人员
        $interestUsers = Classification::where('id',$article->cid)->first()->getInterestUsers()->pluck('user_id')->toArray();
        // 获取协作成员
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
        $users = array_merge($crewArr,$focusUsers);
        // 合并所有通知组用户
        $allUsers = array_unique(array_merge($interestUsers,$users));
        $usersToNotification = User::whereIn('id',$allUsers)->get();
        // 发送通知
        Notification::send($usersToNotification, new ArticleReviewCreatedNotification($review));
    }
}
