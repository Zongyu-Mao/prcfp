<?php

namespace App\Listeners\Personnel\Milestone;

use App\Events\Personnel\Milestone\MilestoneModifiedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Home\Personnel\Milestone;
use App\Models\User;
use Carbon\Carbon;
use App\Home\UserDynamic;
use App\Home\Announcement;
use Illuminate\Support\Facades\Auth;

class MilestoneModifiedListener
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
     * @param  MilestoneModifiedEvent  $event
     * @return void
     */
    public function handle(MilestoneModifiedEvent $event)
    {
        $m = $event->milestone;
        $user = auth('api')->user();
        $a = ($m->created_at==$m->updated_at);
        $behavior = ($a?'创建':'修改').'了里程碑：《'.$m->name.'》。';
        $objectName = $m->name;
        $objectURL = '/personnel/milestone';
        $fromName = '[人事]里程碑';
        $fromURL = $objectURL;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($user->id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 发布公告5代表创建
        if($a)Announcement::announcementAdd(6,5,'里程碑<'.$m->name.'>已经创建。',$objectURL,$createtime);
    }
}
