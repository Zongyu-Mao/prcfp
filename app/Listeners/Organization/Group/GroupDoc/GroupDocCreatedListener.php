<?php

namespace App\Listeners\Organization\Group\GroupDoc;

use App\Events\Organization\Group\GroupDoc\GroupDocCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Organization\Group\GroupDoc\GroupDocCreatedNotification;
use App\Home\Organization\Group\GroupDoc\GroupDocEvent;
use App\Home\Organization\Group\GroupDynamic;
use App\Home\Organization\Group;
use App\Home\Classification;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class GroupDocCreatedListener
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
     * @param  GroupDocCreatedEvent  $event
     * @return void
     */
    public function handle(GroupDocCreatedEvent $event)
    {
        //组织文档的发表，通知组织成员和关注组织用户，写入组织动态和用户动态
        $doc = $event->groupDoc;
        $group = Group::find($doc->gid);
        $c = $doc->created_at==$doc->updated_at;
        $t = $c ? '创建':'更改';
        // 添加事件到用户动态
        $behavior = $t.'了组织文档：';
        $objectName = $doc->title;
        $objectURL = '/organization/groupDocDetail/'.$group->id.'/'.$group->title.'?id='.$doc->id.'&title='.$doc->title;
        $fromName = '组织：'.$group->title;
        $fromURL = '/organization/group/'.$group->id.'/'.$group->title;
        $createtime = Carbon::now();
        $user = User::find($doc->creator_id);
        UserDynamic::dynamicAdd($doc->creator_id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到组织动态
        $groupBehavior = '组织文档已经'.$t.'：';
        GroupDynamic::dynamicAdd($group->id,$group->title,$groupBehavior,$objectName,$objectURL,$createtime);
        GroupDocEvent::groupDocEventAdd($doc->id,$user->id,$user->username,$behavior.$doc->title,$createtime);
        if($c) {
            // 通知创建者创建成功
            // $user->notify(new GroupDocCreatedNotification($doc));
            // 通知组织成员和关注用户
            $users = $group->members()->pluck('user_id')->toArray();
            array_push($users,$group->manage_id);
            $focuses = $group->groupFocus()->pluck('user_id')->toArray();
            array_merge($users,$focuses);
            array_unique($users);
            // $users = array_unique($users);
            $usersToNotification = User::whereIn('id',$users)->get();
            // // User::whereIn('id',$users)->notify(new InterestSpecialtyEntryAdd($result));
            Notification::send($usersToNotification, new GroupDocCreatedNotification($doc));
        }
    }
}
