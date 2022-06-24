<?php

namespace App\Listeners\Organization\Group\Member;

use App\Events\Organization\Group\Member\GroupMemberPositionChangedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Organization\Group\Member\GroupMemberPositionChangedNotification;
use App\Home\Organization\Group;
use App\Home\Organization\Group\GroupDynamic;
use App\Home\Organization\Group\GroupEvent;
use App\Home\Organization\Group\GroupUser;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class GroupMemberPositionChangedListener
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
     * @param  GroupMemberPositionChangedEvent  $event
     * @return void
     */
    public function handle(GroupMemberPositionChangedEvent $event)
    {
        // 成员被调整位置后，写入事件，并发送通知给被调整成员
        $groupUser = $event->groupUser;
        $group = Group::find($groupUser->gid);
        $user = User::find($groupUser->user_id);
        // GroupDynamic::cooperationEventAdd($cooperation->id,$cooperation->manage_id,$cooperation->manager,'已经请出组员<'.$crew->username.'>。');
        // 写入用户动态
        $behavior = '变更组织身份：';
        $objectName = $group->title;
        $objectURL = '/home/organization/groupDetail/'.$group->id.'/'.$group->title;
        $fromName = '组织：'.$group->title;
        $fromURL = '/home/organization/groupDetail/'.$group->id.'/'.$group->title;
        $createtime = Carbon::now();
        // 写入组织事件
        GroupEvent::groupEventAdd($group->id,$user->id,$user->username,'组织身份已经变更。');
        UserDynamic::dynamicAdd($user->id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        $user->notify(new GroupMemberPositionChangedNotification($groupUser));
    }
}
