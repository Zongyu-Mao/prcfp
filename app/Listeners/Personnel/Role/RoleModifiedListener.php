<?php

namespace App\Listeners\Personnel\Role;

use App\Events\Personnel\Role\RoleModifiedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Home\Personnel\Role;
use App\Models\User;
use Carbon\Carbon;
use App\Home\UserDynamic;
use App\Home\Announcement;
use Illuminate\Support\Facades\Auth;

class RoleModifiedListener
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
     * @param  RoleModifiedEvent  $event
     * @return void
     */
    public function handle(RoleModifiedEvent $event)
    {
        //  新建入announcements,modify普通
        // 添加事件到用户动态
        $r = $event->role;
        $a = ($r->created_at==$r->updated_at);
        $user = Auth::user();

        $behavior = ($a?'创建':'修改').'了角色：《'.$r->role.'》。';
        $objectName = $r->role;
        $objectURL = '/personnel/role';
        $fromName = '[人事]角色';
        $fromURL = $objectURL;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($user->id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 发布公告5代表创建
        if($a)Announcement::announcementAdd(6,5,'角色<'.$r->role.'>已经创建。',$objectURL,$createtime);
    }
}
