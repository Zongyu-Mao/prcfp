<?php

namespace App\Listeners\Encyclopedia;

use App\Events\Encyclopedia\EntryCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Encyclopedia\Entry\EntryCreatedNotification;
use App\Notifications\Encyclopedia\Entry\InterestSpecialtyEntryCreatedNotification;
use App\Home\Encyclopedia\Recommend\EntryTemperature;
use App\Home\Announcement;
use App\Home\Classification;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryCreatedListener
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
     * @param  EntryCreatedEvent  $event
     * @return void
     */
    public function handle(EntryCreatedEvent $event)
    {
        $entry = $event->entry;
        // 1代表百科，5代表创建（此时不考虑协作计划创建公告，因为与词条创建是同步的）
        Announcement::announcementAdd('1','5','词条['.$entry->title.']已经创建。','/encyclopedia/reading/'.$entry->id.'/'.$entry->title,$entry->created_at);
        // 初始化热度
        EntryTemperature::recordInitialization($entry->id);
        // 添加事件到用户动态
        $behavior = '创建了词条：';
        $objectName = $entry->title;
        $objectURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        $createtime = Carbon::now();
        $user = User::find($entry->manage_id);
        
        UserDynamic::dynamicAdd($user->id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 通知创建者创建词条成功
        $user->notify(new EntryCreatedNotification($entry));
        // 通知该专业兴趣人员新增了新的词条
        $users = Classification::where('id',$entry->cid)->first()->getInterestUsers()->pluck('user_id')->toArray();
        // if(in_array($creator_id, $users)){
        //     array_forget($users,$creator_id);
        // }
        $users = array_unique($users);
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyEntryAdd($result));
        Notification::send($usersToNotification, new InterestSpecialtyEntryCreatedNotification($entry));
    }
}
