<?php

namespace App\Listeners\Publication;

use App\Events\Publication\ArticleCooperationCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Publication\ArticleCooperation\ArticleCooperationEvent;
use App\Home\Publication\Article\ArticleDynamic;
use App\Home\Publication\Article;
use App\Home\Announcement;
use App\Home\UserDynamic;
use Carbon\Carbon;

class ArticleCooperationCreatedListener
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
     * @param  ArticleCooperationCreatedEvent  $event
     * @return void
     */
    public function handle(ArticleCooperationCreatedEvent $event)
    {
        //协作计划创建成功后，写入协作事件，写入公告，写入著作动态、用户动态
        $article = Article::find($event->articleCooperation->aid);
        $manage_id = $article->manage_id;
        // 添加协作事件
        ArticleCooperationEvent::cooperationEventAdd($event->articleCooperation->id,$event->articleCooperation->creator_id,$event->articleCooperation->creator,'创建了协作计划：['.$event->articleCooperation->title.']。');
        // 更新词条协作计划id
        // Article::where('id',$event->articleCooperation->aid)->update(['cooperation_id' => $event->articleCooperation->id]);
        // 添加事件到用户动态
        $behavior = '开启了协作计划：';
        $objectName = $event->articleCooperation->title;
        $objectURL = '/publication/cooperation/'.$article->id.'/'.$article->title;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($event->articleCooperation->creator_id,$event->articleCooperation->creator,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到著作动态
        $articleBehavior = '著作协作计划'.$objectName.'已经创建：';
        ArticleDynamic::dynamicAdd($article->id,$article->title,$articleBehavior,$objectName,$objectURL,$createtime);
        // 发布公告，1代表百科，2代表著作，1代表协作计划
        Announcement::announcementAdd(2,1,'著作《'.$article->title.'》的协作计划<'.$event->articleCooperation->title.'>已经创建。','/publication/cooperation/'.$article->id.'/'.$article->title,$event->articleCooperation->created_at);
        // 发布通知
        // // 获取词条母专业兴趣人员
        // $interestUsers = Classification::where('id',$article->cid)->first()->getInterestUsers()->pluck('user_id')->toArray();
        // // 获取协作成员
        // $manage_id = $article->manage_id;
        // array_push($interestUsers, $manage_id);
        // // 获取词条的关注用户
        // $focusUsers = $article->articleFocus()->pluck('user_id')->toArray();
        // // 合并所有通知组用户
        // $allUsers = array_unique(array_merge($focusUsers,$interestUsers));
        // $usersToNotification = User::whereIn('id',$allUsers)->get();
        // // 发送通知
        // Notification::send($usersToNotification, new articleCooperationCreatedNotification($event->articleCooperation));

    }
}
