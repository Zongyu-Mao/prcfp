<?php

namespace App\Listeners\Picture;

use App\Events\Picture\PictureEntryLinkedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Picture\PictureTemperatureRecord;
use App\Models\Picture\PictureEntryLink;
use App\Models\Picture\PictureEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryDynamic;
use App\Models\Picture\Picture;

class PictureEntryLinkedListener
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
     * @param  PictureEntryLinkedEvent  $event
     * @return void
     */
    public function handle(PictureEntryLinkedEvent $event)
    {
        $user = auth('api')->user();
        $link = $event->pictureEntryLink;
        $b_id = 82;
        $createtime = Carbon::now();
        PictureTemperatureRecord::recordAdd($link->picture_id,$link->creator_id,$b_id,$createtime);
        $content = '已经创建词条、图片连接';
        PictureEvent::eventAdd($link->picture_id,$user->id,$user->username,$content,$createtime);
        // 添加事件到词条动态
        $e = Entry::find($link->eid);
        $p = Picture::find($link->picture_id);
        $objectName = $p->id;
        $objectURL = '/picture/featuredPictureDetail/'.$p->id.'/'.$p->title;
        EntryDynamic::dynamicAdd($e->id,$e->title,$content,$objectName,$objectURL,$createtime);
    }
}
