<?php

namespace App\Listeners\Encyclopedia\EntryDiscussion;

use App\Events\Encyclopedia\EntryDiscussion\EntryOpponentAcceptedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Encyclopedia\EntryDiscussion\EntryOpponentAcceptedNotification;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryDiscussion\EntryDiscussionEvent;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryOpponentAcceptedListener
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
     * @param  EntryOpponentAcceptedEvent  $event
     * @return void
     */
    public function handle(EntryOpponentAcceptedEvent $event)
    {
        // 词条反对的讨论被接受后，仅通知讨论的作者
        $opp = $event->entryOpponent;
        $entry = Entry::find($opp->eid);
        //反对被接受后，作者的积分和成长值+20
        User::expAndGrowValue($opp->author_id,'20','20');
        //反对被接受后，操作者的积分和成长值+20
        User::expAndGrowValue($opp->recipient_id,'20','20');
        // 添加讨论事件
        EntryDiscussionEvent::discussionEventAdd($entry->id,$opp->recipient_id,$opp->recipient,'接受了['.$opp->author.']提出的反对意见。');
        // 添加事件到用户动态
        $behavior = '接受了百科反对讨论：';
        $objectName = $opp->title;
        $objectURL = '/encyclopedia/discussion/'.$entry->id.'/'.$entry->title.'#discussionOpponent'.$opp->id;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($opp->recipient_id,$opp->recipient,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 词条添加热度记录
        $b_id = 49;
        EntryTemperatureRecord::recordAdd($entry->id,$opp->author_id,$b_id,$createtime);
        // 给反对作者发送通知
        User::find($opp->author_id)->notify(new EntryOpponentAcceptedNotification($opp));
        // $manage_id = $entry->manage_id;
        // if(count($cooperation)){
        //     $crewArr = $cooperation->crews()->pluck('user_id')->toArray();
        //     $initiate_id = $cooperation->manage_id;
        //     array_push($crewArr, $manage_id);
        //     array_push($crewArr, $initiate_id); 
        // }else{
        //     $crewArr = [];
        //     array_push($crewArr, $manage_id);
        // }
        // // 获取词条的关注用户
        // $focusUsers = $entry->entryFocus()->pluck('user_id')->toArray();
        // // 合并协作组与兴趣用户
        // $users = array_unique(array_merge($crewArr,$focusUsers));
        // $usersToNotification = User::whereIn('id',$users)->get();
        // // User::whereIn('id',$users)->notify(new InterestSpecialtyEntryAdd($result));
        // Notification::send($usersToNotification, new EntryOpponentAcceptedNotification($opp));
    }
}
