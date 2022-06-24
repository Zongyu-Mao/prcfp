<?php

namespace App\Listeners\Organization\Group\Member;

use App\Events\Organization\Group\Member\GroupMemberJoinedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Organization\Group;
use App\Home\Organization\Group\GroupDynamic;
use App\Home\Organization\Group\GroupEvent;
use App\Home\Organization\Group\GroupUser;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class GroupMemberJoinedListener
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
     * @param  GroupMemberJoinedEvent  $event
     * @return void
     */
    public function handle(GroupMemberJoinedEvent $event)
    {
        // 成功写入协作成员后，触发事件：协作事件，著作动态，用户动态；此处暂时不产生通知
        $groupUser = $event->groupUser;
        $group = Group::find($groupUser->gid);
        $user = User::find($groupUser->user_id);
        $creatime = Carbon::now();
        // 写入组织事件
        GroupEvent::groupEventAdd($group->id,$user->id,$user->username,'成功加入组织，大家合作愉快。');
        // 添加事件到用户动态
        $behavior = '加入了组织：';
        $objectName = $group->title;
        $objectURL = '/home/organization/groupDetail/'.$group->id.'/'.$group->title;
        $fromName = '组织'.$group->title;
        $fromURL = '/home/organization/groupDetail/'.$group->id.'/'.$group->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($user->id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到组织动态
        $groupBehavior = '新增成员：['.$user->username.']';
        GroupDynamic::dynamicAdd($group->id,$group->title,$groupBehavior,$objectName,$objectURL,$createtime);
    }
}
