<?php

namespace App\Listeners\Encyclopedia\EntryReview;

use App\Events\Encyclopedia\EntryReview\EntryReviewAdvisementRejectedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Encyclopedia\EntryReview\EntryReviewAdvisementRejectedToUserNotification;
use App\Notifications\Encyclopedia\EntryReview\EntryReviewAdvisementRejectedNotification;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\EntryReview;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryReview\entryReviewAdvise;
use App\Home\Encyclopedia\EntryReview\EntryReviewEvent;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryReviewAdvisementRejectedListener
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
     * @param  EntryReviewAdvisementRejectedEvent  $event
     * @return void
     */
    public function handle(EntryReviewAdvisementRejectedEvent $event)
    {
        //评审计划建议的拒绝，应通知协作小组成员和原作者
        $advise = $event->entryReviewAdvise;
        $entryReview = EntryReview::find($advise->rid);
        $parentReviewAdvise = EntryReviewAdvise::find($advise->pid);
        $entry = Entry::find($entryReview->eid);
        $cooperation = EntryCooperation::find($entry->cooperation_id);
        // 添加事件到用户动态
        $behavior = '拒绝评审计划建议：';
        $objectName = $advise->title;
        $objectURL = '/encyclopedia/review/'.$entry->id.'/'.$entry->title.'#reviewAdvise'.$advise->id;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($advise->author_id,$advise->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        //发表了有效的讨论后，积分和成长值+100
        User::expAndGrowValue($advise->author_id,'100','100');
        // 添加讨论事件
        EntryReviewEvent::reviewEventAdd($entryReview->id,$advise->author_id,$advise->author,'拒绝了['.$advise->recipient.']提出的建议：<'.$parentReviewAdvise->title.'>，理由：<'.$advise->title.'>。');
        // 通知原作者被拒绝
        User::find($advise->recipient_id)->notify(new EntryReviewAdvisementRejectedToUserNotification($advise));
        // 词条添加热度记录
        $b_id = 30;
        EntryTemperatureRecord::recordAdd($entry->id,$advise->author_id,$b_id,$createtime);
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
        // 合并协作组
        $users = array_unique($crewArr);
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyEntryAdd($result));
        Notification::send($usersToNotification, new EntryReviewAdvisementRejectedNotification($advise));
    }
}
