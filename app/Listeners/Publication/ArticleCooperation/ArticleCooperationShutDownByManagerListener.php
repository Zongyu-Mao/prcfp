<?php

namespace App\Listeners\Publication\ArticleCooperation;

use App\Events\Publication\ArticleCooperation\ArticleCooperationShutDownByManagerEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Publication\ArticleCooperation\ArticleCooperationShutDownByManagerNotification;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleCooperation\ArticleCooperationEvent;
use App\Home\Classification;
use App\Home\Announcement;
use App\Home\UserDynamic;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Home\Personnel\MedalSuit;
use App\Home\Personnel\Inform\PunishRecord;

class ArticleCooperationShutDownByManagerListener
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
     * @param  ArticleCooperationShutDownByManagerEvent  $event
     * @return void
     */
    public function handle(ArticleCooperationShutDownByManagerEvent $event)
    {
        $coo = $event->articleCooperation;
        $content = Article::find($coo->aid);
        $user = auth('api')->user();
        $createtime = Carbon::now();
        // 拿到惩示章
        $medal_id = MedalSuit::where('type',4)->with('getMedals')->first()->getMedals->where('sort',1)->first()->id;
        // 显然这里的inform_id只能设置为0了，看看后期改进不
        $type=4;
        $endtime = Carbon::now()->addMonths($content->level);
        PunishRecord::punishRecordAdd($medal_id,$user->id,0,$type,$endtime,$createtime);

        // 主动关闭，添加协作事件
        ArticleCooperationEvent::cooperationEventAdd($coo->id,$user->id,$user->username,'(自管理员)主动关闭并退出了协作计划。');
        // 添加事件到用户动态
        $behavior = '主动退出并关闭了协作计划：';
        $objectName = $coo->title;
        $objectURL = '/publication/cooperation/'.$content->id.'/'.$content->title;
        $fromName = '著作主内容：'.$content->title;
        $fromURL = '/publication/reading/'.$content->id.'/'.$content->title;
        UserDynamic::dynamicAdd($user->id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 发布公告
        Announcement::announcementAdd(2,1,'著作《'.$content->title.'》的协作计划<'.$coo->title.'>已经关闭,自管理员已经退出内容自管理。','/publication/cooperation/'.$content->id.'/'.$content->title,$createtime);
        // 获取协作成员（通知对象）
        $crewArr = $coo->crews()->pluck('user_id')->toArray();
        // 发送通知
        $usersToNotification = User::whereIn('id',$crewArr)->get();
        Notification::send($usersToNotification, new ArticleCooperationShutDownByManagerNotification($coo));
    }
}
