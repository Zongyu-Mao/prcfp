<?php

namespace App\Listeners\Encyclopedia\EntryReview;

use App\Events\Encyclopedia\EntryReview\EntryReviewDiscussionCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Encyclopedia\EntryReview\EntryReviewDiscussionCreatedNotification;
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

class EntryReviewDiscussionCreatedListener
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
     * @param  EntryReviewDiscussionCreatedEvent  $event
     * @return void
     */
    public function handle(EntryReviewDiscussionCreatedEvent $event)
    {
        //这里触发评审中立及支持事件，另外也触发评审的普通回复事件
        //中立票比较简单，投票成功后，通知相关用户即可
        $discussion = $event->entryReviewDiscussion;
        $entryReview = EntryReview::find($discussion->rid);
        $entry = Entry::find($entryReview->eid);
        $cooperation = EntryCooperation::find($entry->cooperation_id);
        //发表了有效的评审意见后，积分和成长值+50
        User::expAndGrowValue($discussion->author_id,'50','50');
        // 添加评审事件
        if($discussion->standpoint == '1'){
            EntryReviewEvent::reviewEventAdd($discussion->rid,$discussion->author_id,$discussion->getAuthor->username,'支持本次评审计划通过。');
            $behavior = '发表评审计划支持意见：';
            $entryBehavior = '新增评审计划支持意见';
        }elseif($discussion->standpoint == '3'){
            EntryReviewEvent::reviewEventAdd($discussion->rid,$discussion->author_id,$discussion->getAuthor->username,'投票并保持中立立场。');
            $behavior = '发表评审计划中立意见：';
            $entryBehavior = '新增评审计划中立意见';
        }

        // 添加事件到用户动态
        $objectName = $entryReview->title;
        $objectURL = '/encyclopedia/review/'.$entry->id.'/'.$entry->title.'#reviewDiscussion'.$discussion->id;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($discussion->author_id,$discussion->getAuthor->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        EntryDynamic::dynamicAdd($entry->id,$entry->title,$entryBehavior,$objectName,$objectURL,$createtime);
        // 词条添加热度记录
        $b_id = 27;
        EntryTemperatureRecord::recordAdd($entry->id,$discussion->author_id,$b_id,$createtime);
        // 开启对协作组成员和关注词条用户的通知
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
        Notification::send($usersToNotification, new EntryReviewDiscussionCreatedNotification($discussion));
    }
}
