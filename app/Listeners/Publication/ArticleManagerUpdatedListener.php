<?php

namespace App\Listeners\Publication;

use App\Events\Publication\ArticleManagerUpdatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Home\Publication\Article\ArticleDynamic;
use App\Home\Announcement;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleManagerUpdatedListener
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
     * @param  ArticleManagerUpdatedEvent  $event
     * @return void
     */
    public function handle(ArticleManagerUpdatedEvent $event)
    {
        $c = $event->article;
        $user = $c->managerInfo;
        $c_url = '/publication/reading/'.$c->id.'/'.$c->title;
        Announcement::announcementAdd(2,6,'著作['.$c->title.']已经变更新的自管理员['.$user->username.']。',$c_url,$c->updated_at);
        // 添加事件到用户动态
        $behavior = '接管自管理著作：';
        $objectName = $c->title;
        $objectURL = $c_url;
        $fromName = '著作：'.$c->title;
        $fromURL = $c_url;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($user->id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        $d_ehavior = '变更自管理员';
        ArticleDynamic::dynamicAdd($c->id,$c->title,$d_ehavior,$objectName,$objectURL,$createtime);
    }
}
