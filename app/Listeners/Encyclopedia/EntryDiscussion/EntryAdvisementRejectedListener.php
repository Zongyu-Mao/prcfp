<?php

namespace App\Listeners\Encyclopedia\EntryDiscussion;

use App\Events\Encyclopedia\EntryDiscussion\EntryAdvisementRejectedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Encyclopedia\EntryDiscussion\EntryAdvisementRejectedToUserNotification;
use App\Notifications\Encyclopedia\EntryDiscussion\EntryAdvisementRejectedNotification;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryDiscussion\EntryDiscussionEvent;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryAdvisementRejectedListener
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
     * @param  EntryAdvisementRejectedEvent  $event
     * @return void
     */
    public function handle(EntryAdvisementRejectedEvent $event)
    {
        // 词条建议的讨论被拒绝后，通知原建议作者及协作小组
        $advise = $event->entryAdvise;
        $entry = Entry::find($advise->eid);
        $cooperation = EntryCooperation::find($entry->cooperation_id);
        // 添加事件到用户动态
        $behavior = '拒绝了百科建议讨论：';
        $objectName = $advise->title;
        $objectURL = '/encyclopedia/discussion/'.$entry->id.'/'.$entry->title.'#discussionAdvise'.$advise->id;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($advise->author_id,$advise->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 词条添加热度记录
        $b_id = 53;
        EntryTemperatureRecord::recordAdd($entry->id,$advise->author_id,$b_id,$createtime);
        //反对被拒绝后，操作者的积分和成长值+20
        User::expAndGrowValue($advise->author_id,'100','100');
        // 添加讨论事件
        EntryDiscussionEvent::discussionEventAdd($entry->id,$advise->author_id,$advise->author,'拒绝了['.$advise->recipient.']提出的反对意见。');
        // 给反对作者发送通知
        User::find($advise->recipient_id)->notify(new EntryAdvisementRejectedToUserNotification($advise));
        // 开启对协作组成员的通知
        $manage_id = $entry->manage_id;
        if($cooperation){
            $crewArr = $cooperation->crews()->pluck('user_id')->toArray();
            $initiate_id = $cooperation->manage_id;
            array_push($crewArr, $manage_id);
            array_push($crewArr, $initiate_id); 
        }else{
            $crewArr = [];
            array_push($crewArr, $manage_id);
        }
        // 获取词条的关注用户
        // $focusUsers = $entry->entryFocus()->pluck('user_id')->toArray();
        // 合并协作组与兴趣用户
        // $users = array_unique(array_merge($crewArr,$focusUsers));
        $users = array_unique($crewArr);
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyEntryAdd($result));
        Notification::send($usersToNotification, new EntryAdvisementRejectedNotification($advise));
    }
}
