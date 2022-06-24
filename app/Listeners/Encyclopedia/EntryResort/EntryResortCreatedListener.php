<?php

namespace App\Listeners\Encyclopedia\EntryResort;

use App\Events\Encyclopedia\EntryResort\EntryResortCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Encyclopedia\EntryResort\EntryResortCreatedNotification;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryResort\EntryResortEvent;
use App\Home\Encyclopedia\EntryDynamic;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\Classification;
use App\Home\UserDynamic;
use App\Home\Announcement;
use Carbon\Carbon;
use App\Models\User;

class EntryResortCreatedListener
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
     * @param  EntryResortCreatedEvent  $event
     * @return void
     */
    public function handle(EntryResortCreatedEvent $event)
    {
        //词条创建成功后，写入求助事件，写入公告，通知兴趣用户，求助不会通知协作小组（因为协作小组已经默认关注了词条了），但是求助通知所有与本词条有关的用户
        $resort = $event->entryResort;
        $entry = Entry::find($resort->eid);
        $manage_id = $entry->manage_id;
        //添加事件到求助事件表
        EntryResortEvent::resortEventAdd($resort->eid,$resort->author_id,$resort->author,'发布了求助内容:《'.$resort->title.'》。');
        //发表了有效的讨论后，积分和成长值+20
        User::expAndGrowValue($resort->author_id,'20','20');
        // 添加事件到用户动态
        $behavior = '发布了求助内容：';
        $objectName = $resort->title;
        $objectURL = '/encyclopedia/resort/'.$entry->id.'/'.$entry->title.'#resort'.$resort->id;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/resort/'.$entry->id.'/'.$entry->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($resort->author_id,$resort->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $entryBehavior = '新增了求助内容';
        EntryDynamic::dynamicAdd($entry->id,$entry->title,$entryBehavior,$objectName,$objectURL,$createtime);
        // 发布公告，1代表百科，4代表求助
        Announcement::announcementAdd('1','4','词条《'.$entry->title.'》新增求助内容<'.$resort->title.'>。','/encyclopedia/resort/'.$entry->id.'/'.$entry->title.'#'.$resort->id,$resort->created_at);
        // 词条添加热度记录
        $b_id = 40;
        EntryTemperatureRecord::recordAdd($entry->id,$resort->author_id,$b_id,$createtime);
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
        Notification::send($usersToNotification, new EntryResortCreatedNotification($resort));
    }
}
