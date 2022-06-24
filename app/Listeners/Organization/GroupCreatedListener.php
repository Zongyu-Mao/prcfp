<?php

namespace App\Listeners\Organization;

use App\Events\Organization\GroupCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Organization\GroupCreatedNotification;
use App\Notifications\Organization\InterestSpecialtyGroupCreatedNotification;
use App\Home\Organization\Group\GroupDynamic;
use App\Home\Announcement;
use App\Home\Classification;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class GroupCreatedListener
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
     * @param  GroupCreatedEvent  $event
     * @return void
     */
    public function handle(GroupCreatedEvent $event)
    {
        //创建组织，公告、通知，写入组织动态和用户动态
        //组织成功创建后
        // 4代表组织，0代表编辑,5代表创建
        $group = $event->group;
        Announcement::announcementAdd('4','5','组织['.$group->title.']已经创建。','/home/organization/group/'.$group->id.'/'.$group->title,$group->created_at);
        // 添加事件到用户动态
        $behavior = '创建了组织：';
        $objectName = $group->title;
        $objectURL = '/organization/group/'.$group->id.'/'.$group->title;
        $fromName = '组织：'.$group->title;
        $fromURL = '/organization/group/'.$group->id.'/'.$group->title;
        $createtime = Carbon::now();
        $user = User::find($group->creator_id);
        UserDynamic::dynamicAdd($group->creator_id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到著作动态
        $groupBehavior = '组织已经创建：';
        GroupDynamic::dynamicAdd($group->id,$group->title,$groupBehavior,$objectName,$objectURL,$createtime);
        // 通知创建者创建成功
        $user->notify(new GroupCreatedNotification($group));
        // 通知该专业兴趣人员新增了新的组织
        $users = Classification::where('id',$group->cid)->first()->getInterestUsers()->pluck('user_id')->toArray();
        // if(in_array($creator_id, $users)){
        //     array_forget($users,$creator_id);
        // }
        $users = array_unique($users);
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyEntryAdd($result));
        Notification::send($usersToNotification, new InterestSpecialtyGroupCreatedNotification($group));
    }
}
