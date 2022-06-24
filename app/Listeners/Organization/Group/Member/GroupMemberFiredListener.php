<?php

namespace App\Listeners\Organization\Group\Member;

use App\Events\Organization\Group\Member\GroupMemberFiredEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Organization\Group;
use App\Home\Organization\Group\GroupDynamic;
use App\Notifications\Organization\Group\Member\GroupMemberFiredNotification;
use App\Home\Organization\Group\GroupUser;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class GroupMemberFiredListener
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
     * @param  GroupMemberFiredEvent  $event
     * @return void
     */
    public function handle(GroupMemberFiredEvent $event)
    {
        // 成员被请出后，写入事件，并发送通知给被请出组员
        $groupUser = $event->groupUser;
        $group = Group::find($groupUser->gid);
        $user = User::find($groupUser->user_id);
        // GroupDynamic::cooperationEventAdd($cooperation->id,$cooperation->manage_id,$cooperation->manager,'已经请出组员<'.$crew->username.'>。');
        // 写入用户动态
        $behavior = '退出组织：';
        $objectName = $group->title;
        $objectURL = '/home/organization/groupDetail/'.$group->id.'/'.$group->title;
        $fromName = '组织：'.$group->title;
        $fromURL = '/home/organization/groupDetail/'.$group->id.'/'.$group->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($user->id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        $user->notify(new GroupMemberFiredNotification($groupUser));
    }
}
