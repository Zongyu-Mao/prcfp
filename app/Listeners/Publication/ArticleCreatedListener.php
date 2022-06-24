<?php

namespace App\Listeners\Publication;

use App\Events\Publication\ArticleCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Publication\Article\ArticleCreatedNotification;
use App\Notifications\Publication\Article\InterestSpecialtyArticleCreatedNotification;
use App\Home\Publication\ArticleCooperation\ArticleCooperationEvent;
use App\Home\Publication\Article\ArticleDynamic;
use App\Home\Publication\ArticleCooperation;
use App\Home\Announcement;
use App\Home\Classification;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleCreatedListener
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
     * @param  ArticleCreatedEvent  $event
     * @return void
     */
    public function handle(ArticleCreatedEvent $event)
    {
        //著作成功创建后
        // 2代表著作，5代表创建（此时不考虑协作计划创建公告，因为与词条创建是同步的）
        Announcement::announcementAdd('2','5','著作['.$event->article->title.']已经创建。','/publication/reading/'.$event->article->id.'/'.$event->article->title,$event->article->created_at);
        // 添加事件到用户动态
        $behavior = '创建了著作：';
        $objectName = $event->article->title;
        $objectURL = '/publication/reading/'.$event->article->id.'/'.$event->article->title;
        $fromName = '著作：'.$event->article->title;
        $fromURL = '/publication/reading/'.$event->article->id.'/'.$event->article->title;
        $createtime = Carbon::now();
        $user = User::find($event->article->creator_id);
        UserDynamic::dynamicAdd($event->article->creator_id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到著作动态
        $articleBehavior = '著作已经创建：';
        ArticleDynamic::dynamicAdd($event->article->id,$event->article->title,$articleBehavior,$objectName,$objectURL,$createtime);
        // 通知创建者创建成功
        $user->notify(new ArticleCreatedNotification($event->article));
        // 通知该专业兴趣人员新增了新的著作
        $users = Classification::where('id',$event->article->cid)->first()->getInterestUsers()->pluck('user_id')->toArray();
        // if(in_array($creator_id, $users)){
        //     array_forget($users,$creator_id);
        // }
        $users = array_unique($users);
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyEntryAdd($result));
        Notification::send($usersToNotification, new InterestSpecialtyArticleCreatedNotification($event->article));
    }
}
