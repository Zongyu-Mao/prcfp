<?php

namespace App\Listeners\Personnel\Level;

use App\Events\Personnel\Level\LevelModifiedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Home\Personnel\Level;
use App\Models\User;
use Carbon\Carbon;
use App\Home\UserDynamic;
use App\Home\Announcement;
use Illuminate\Support\Facades\Auth;

class LevelModifiedListener
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
     * @param  LevelModifiedEvent  $event
     * @return void
     */
    public function handle(LevelModifiedEvent $event)
    {
        $l = $event->level;
        $user = auth('api')->user();
        $a = ($l->created_at==$l->updated_at);
        $behavior = ($a?'创建':'修改').'了等级制：《'.$l->name.'》。';
        $objectName = $l->name;
        $objectURL = '/personnel/level';
        $fromName = '[人事]等级制';
        $fromURL = $objectURL;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($user->id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 发布公告5代表创建
        if($a)Announcement::announcementAdd(6,5,'等级制<'.$l->name.'>已经创建。',$objectURL,$createtime);
    }
}
