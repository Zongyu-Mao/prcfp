<?php

namespace App\Listeners\Encyclopedia\EntryDiscussion;

use App\Events\Encyclopedia\EntryDiscussion\EntryOpponentRejectedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Encyclopedia\EntryDiscussion\EntryOpponentRejectedToUserNotification;
use App\Notifications\Encyclopedia\EntryDiscussion\EntryOpponentRejectedNotification;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryDiscussion\EntryDiscussionEvent;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryOpponentRejectedListener
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
     * @param  EntryOpponentRejectedEvent  $event
     * @return void
     */
    public function handle(EntryOpponentRejectedEvent $event)
    {
        // 词条反对的讨论被拒绝后，原则上应通知所有相关用户，由于拒绝评论是新建的，因此原讨论的接受者与作者身份互换
        $opp = $event->entryOpponent;
        $entry = Entry::find($opp->eid);
        $cooperation = EntryCooperation::find($entry->cooperation_id);
        //反对被拒绝后，操作者的积分和成长值+20
        User::expAndGrowValue($opp->author_id,'100','100');
        // 添加讨论事件
        EntryDiscussionEvent::discussionEventAdd($entry->id,$opp->author_id,$opp->author,'拒绝了['.$opp->recipient.']提出的反对意见。');
        // 添加事件到用户动态
        $behavior = '拒绝了百科反对讨论：';
        $objectName = $opp->title;
        $objectURL = '/encyclopedia/discussion/'.$entry->id.'/'.$entry->title.'#discussionOpponent'.$opp->id;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($opp->recipient_id,$opp->recipient,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 给反对作者发送通知
        User::find($opp->recipient_id)->notify(new EntryOpponentRejectedToUserNotification($opp));
        // 词条添加热度记录
        $b_id = 50;
        EntryTemperatureRecord::recordAdd($entry->id,$opp->author_id,$b_id,$createtime);
        // 通知词条相关用户
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
        $focusUsers = $entry->entryFocus()->pluck('user_id')->toArray();
        // 合并协作组与兴趣用户
        $users = array_unique(array_merge($crewArr,$focusUsers));
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyEntryAdd($result));
        Notification::send($usersToNotification, new EntryOpponentRejectedNotification($opp));
    }
}
