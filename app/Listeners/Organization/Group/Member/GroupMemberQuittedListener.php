<?php

namespace App\Listeners\Organization\Group\Member;

use App\Events\Organization\Group\Member\GroupMemberQuittedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Organization\Group;
use App\Home\Organization\Group\GroupDynamic;
use App\Home\Organization\Group\GroupEvent;
use App\Home\Organization\Group\GroupUser;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class GroupMemberQuittedListener
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
     * @param  GroupMemberQuittedEvent  $event
     * @return void
     */
    public function handle(GroupMemberQuittedEvent $event)
    {
        // 组员退出后，写入事件，并发送通知给被请出组员
        $groupUser = $event->groupUser;
        $group = Group::find($groupUser->gid);
        $crew = User::find($groupUser->user_id);
        GroupEvent::groupEventAdd($group->id,$crew->id,$crew->username,'退出协作计划。');
        // 写入用户动态
        $behavior = '退出组织：';
        $objectName = $group->title;
        $objectURL = '/home/organization/groupDetail/'.$group->id.'/'.$group->title;
        $fromName = '组织：'.$group->title;
        $fromURL = '/home/organization/groupDetail/'.$group->id.'/'.$group->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($crew->id,$crew->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
    }
}
