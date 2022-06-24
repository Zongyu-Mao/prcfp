<?php

namespace App\Listeners\Organization\Group;

use App\Events\Organization\Group\GroupIntroModifiedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Organization\Group;
use App\Home\Organization\Group\GroupDynamic;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class GroupIntroModifiedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    /**
     * Handle the event.
     *
     * @param  GroupIntroModifiedEvent  $event
     * @return void
     */
    public function handle(GroupIntroModifiedEvent $event)
    {
        // 介绍修改，只需加入动态即可
        $group = $event->group;
        $behavior = '修改了组织介绍：';
        $objectName = $group->title;
        $objectURL = '/organization/group/'.$group->id.'/'.$group->title;
        $fromName = '组织：'.$group->title;
        $fromURL = '/organization/group/'.$group->id.'/'.$group->title;
        $createtime = Carbon::now();
        $user = User::find($group->creator_id);
        UserDynamic::dynamicAdd($group->creator_id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到组织动态
        $groupBehavior = '组织介绍已经修改：';
        GroupDynamic::dynamicAdd($group->id,$group->title,$groupBehavior,$objectName,$objectURL,$createtime);
    }
}
