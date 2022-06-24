<?php

namespace App\Listeners\Personnel\Prop;

use App\Events\Personnel\Prop\PropModifiedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Home\Personnel\Prop;
use App\Models\User;
use Carbon\Carbon;
use App\Home\UserDynamic;
use App\Home\Announcement;
use Illuminate\Support\Facades\Auth;

class PropModifiedListener
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
     * @param  PropModifiedEvent  $event
     * @return void
     */
    public function handle(PropModifiedEvent $event)
    {
        $p = $event->prop;
        $user = auth('api')->user();
        $a = ($p->created_at==$p->updated_at);
        $behavior = ($a?'创建':'修改').'了道具：《'.$p->name.'》。';
        $objectName = $p->name;
        $objectURL = '/personnel/prop';
        $fromName = '[人事]道具';
        $fromURL = $objectURL;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($user->id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 发布公告5代表创建
        if($a)Announcement::announcementAdd(6,5,'道具<'.$p->name.'>已经创建。',$objectURL,$createtime);
    }
}
