<?php

namespace App\Listeners\Encyclopedia\EntryReview;

use App\Events\Encyclopedia\EntryReview\EntryReviewOpponentAcceptedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Encyclopedia\EntryReview\EntryReviewOpponentAcceptedToUserNotification;
use App\Notifications\Encyclopedia\EntryReview\EntryReviewOpponentAcceptedNotification;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\EntryReview;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryReview\EntryReviewEvent;
use App\Home\Encyclopedia\EntryDynamic;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryReviewOpponentAcceptedListener
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
     * @param  EntryReviewOpponentAcceptedEvent  $event
     * @return void
     */
    public function handle(EntryReviewOpponentAcceptedEvent $event)
    {
        //反对意见的接受，应通知协作组成员和原作者
        $opp = $event->entryReviewOpponent;
        $entryReview = EntryReview::find($opp->rid);
        $entry = Entry::find($entryReview->eid);
        $cooperation = EntryCooperation::find($entry->cooperation_id);
        $createtime = Carbon::now();
        // 添加到用户动态
        $behavior = '接受了评审计划反对意见：';
        $objectName = $opp->title;
        $objectURL = '/encyclopedia/review/'.$entry->id.'/'.$entry->title.'#reviewOpponent'.$opp->id;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        UserDynamic::dynamicAdd($opp->recipient_id,$opp->recipient,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        //发表了有效的讨论后，积分和成长值+100
        User::expAndGrowValue($opp->author_id,'100','100');
        // 词条添加热度记录
        $b_id = 26;
        EntryTemperatureRecord::recordAdd($entry->id,$opp->recipient_id,$b_id,$createtime);
        // 添加讨论事件
        EntryReviewEvent::reviewEventAdd($entryReview->id,$opp->recipient_id,$opp->recipient,'接受了['.$opp->author.']提出的反对意见：<'.$opp->title.'>。');
        // 通知原反对作者被拒绝
        User::find($opp->author_id)->notify(new EntryReviewOpponentAcceptedToUserNotification($opp));
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
        $users = array_unique($crewArr);
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyEntryAdd($result));
        Notification::send($usersToNotification, new EntryReviewOpponentAcceptedNotification($opp));
    }
}
