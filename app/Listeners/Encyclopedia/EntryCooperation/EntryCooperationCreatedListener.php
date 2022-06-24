<?php

namespace App\Listeners\Encyclopedia\EntryCooperation;

use App\Events\Encyclopedia\EntryCooperation\EntryCooperationCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Encyclopedia\EntryCooperation\EntryCooperationCreatedNotification;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationEvent;
use App\Home\Classification;
use App\Home\Announcement;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryCooperationCreatedListener
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
     * @param  EntryCooperationCreatedEvent  $event
     * @return void
     */
    public function handle(EntryCooperationCreatedEvent $event)
    {
        //协作计划创建成功后，写入协作事件，写入公告，通知兴趣用户，如果是创建协作计划，此时协作小组一定不存在，因此不必记入协作小组，只需记入词条自管理员
        $entry = Entry::find($event->entryCooperation->eid);
        $manage_id = $entry->manage_id;
        // 添加协作事件
        EntryCooperationEvent::cooperationEventAdd($event->entryCooperation->id,$event->entryCooperation->creator_id,$event->entryCooperation->creator,'创建了协作计划：['.$event->entryCooperation->title.']。');
        // 更新词条协作计划id
        Entry::where('id',$event->entryCooperation->eid)->update(['cooperation_id' => $event->entryCooperation->id]);
        // 添加事件到用户动态
        $behavior = '开启了协作计划：';
        $objectName = $event->entryCooperation->title;
        $objectURL = '/encyclopedia/cooperation/'.$entry->id.'/'.$entry->title;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($event->entryCooperation->creator_id,$event->entryCooperation->creator,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 发布公告，1代表百科，1代表协作计划
        Announcement::announcementAdd('1','1','词条《'.$entry->title.'》的协作计划<'.$event->entryCooperation->title.'>已经创建。','/encyclopedia/cooperation/'.$entry->id.'/'.$entry->title,$event->entryCooperation->created_at);
        // 发布通知
        // 获取词条母专业兴趣人员
        $interestUsers = Classification::where('id',$entry->cid)->first()->getInterestUsers()->pluck('user_id')->toArray();
        // 获取协作成员
        $manage_id = $entry->manage_id;
        array_push($interestUsers, $manage_id);
        // 获取词条的关注用户
        $focusUsers = $entry->entryFocus()->pluck('user_id')->toArray();
        // 合并所有通知组用户
        $allUsers = array_unique(array_merge($focusUsers,$interestUsers));
        $usersToNotification = User::whereIn('id',$allUsers)->get();
        // 发送通知
        Notification::send($usersToNotification, new EntryCooperationCreatedNotification($event->entryCooperation));
    }
}
