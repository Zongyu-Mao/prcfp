<?php

namespace App\Listeners\Encyclopedia\EntryReview;

use App\Events\Encyclopedia\EntryReview\EntryReviewOpponentRejectedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Encyclopedia\EntryReview\EntryReviewOpponentRejectedToUserNotification;
use App\Notifications\Encyclopedia\EntryReview\EntryReviewOpponentRejectedNotification;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\EntryReview;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryReview\entryReviewOpponent;
use App\Home\Encyclopedia\EntryReview\EntryReviewEvent;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\Encyclopedia\EntryDynamic;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryReviewOpponentRejectedListener
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
     * @param  EntryReviewOpponentRejectedEvent  $event
     * @return void
     */
    public function handle(EntryReviewOpponentRejectedEvent $event)
    {
        //评审计划反对意见的创建，应通知协作小组成员和原作者
        $opp = $event->entryReviewOpponent;
        $entryReview = EntryReview::find($opp->rid);
        $parentReviewOpponent = entryReviewOpponent::find($opp->pid);
        $entry = Entry::find($entryReview->eid);
        $createtime = Carbon::now();
        // 添加到用户动态
        $behavior = '拒绝了评审计划反对意见：';
        $objectName = $opp->title;
        $objectURL = '/encyclopedia/review/'.$entry->id.'/'.$entry->title.'#reviewOpponent'.$opp->id;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        UserDynamic::dynamicAdd($opp->author_id,$opp->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);

        $cooperation = EntryCooperation::find($entry->cooperation_id);
        //发表了有效的讨论后，积分和成长值+100
        User::expAndGrowValue($opp->author_id,'100','100');
        // 词条添加热度记录
        $b_id = 25;
        EntryTemperatureRecord::recordAdd($entry->id,$opp->author_id,$b_id,$createtime);
        // 添加讨论事件
        EntryReviewEvent::reviewEventAdd($entryReview->id,$opp->author_id,$opp->author,'拒绝了['.$opp->recipient.']提出的反对意见：<'.$parentReviewOpponent->title.'>，理由：<'.$opp->title.'>。');
        // 通知原反对作者被拒绝
        User::find($opp->recipient_id)->notify(new EntryReviewOpponentRejectedToUserNotification($opp));
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
        Notification::send($usersToNotification, new EntryReviewOpponentRejectedNotification($opp));
    }
}
