<?php

namespace App\Listeners\Picture;

use App\Events\Picture\PictureCreatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Picture\Picture;
use App\Models\Picture\PictureEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Home\Announcement;

class PictureCreatedListener
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
     * @param  PictureCreatedEvent  $event
     * @return void
     */
    public function handle(PictureCreatedEvent $event)
    {
        // 添加事件到用户动态
        $user = auth('api')->user();
        $p = $event->picture;
        $behavior = '创建图片:';
        $objectName = $p->title;
        $objectURL = '/picture/featuredPictureDetail/'.$p->id.'/'.$p->title;
        $fromName = '图片'.$p->title;
        $fromURL = $objectURL;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($user->id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $content = '图片已经创建';
        PictureEvent::eventAdd($p->id,$user->id,$user->username,$content,$createtime);
        Announcement::announcementAdd(9,5,'图片['.$p->title.']已经创建。','/picture/featuredPictureDetail/'.$p->id.'/'.$p->title,$p->created_at);
    }
}
