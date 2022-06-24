<?php

namespace App\Listeners\Encyclopedia\EntryReview;

use App\Events\Encyclopedia\EntryReview\EntryReviewCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Encyclopedia\EntryReview\EntryReviewCreatedNotification;
use App\Home\Encyclopedia\EntryReview\EntryReviewEvent;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\Announcement;
use App\Home\Classification;
use App\Home\Encyclopedia\EntryDynamic;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryReviewCreatedListener
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
     * @param  EntryReviewCreatedEvent  $event
     * @return void
     */
    public function handle(EntryReviewCreatedEvent $event)
    {
        //评审计划创建后，通知协作组成员和词条关注用户评审计划创建成功
        $review = $event->entryReview;
        $entry = Entry::find($review->eid);
        $cooperation = EntryCooperation::find($entry->cooperation_id);
        // 添加事件到用户动态
        $behavior = '开启评审计划：';
        $objectName = $review->title;
        $objectURL = '/encyclopedia/review/'.$entry->id.'/'.$entry->title;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($review->initiate_id,$review->initiater,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $entryBehavior = '开启评审计划';
        EntryDynamic::dynamicAdd($entry->id,$entry->title,$entryBehavior,$objectName,$objectURL,$createtime);
        // 发布公告，1代表百科，2代表评审计划
        Announcement::announcementAdd('1','2','词条《'.$entry->title.'》的评审计划<'.$review->title.'>已经创建。','/encyclopedia/review/'.$entry->id.'/'.$entry->title,$review->created_at);
        Entry::where('id',$review->eid)->update(['review_id' => $review->id]);
        // 增加用户积分
        User::expAndGrowValue($review->initiate_id,'100','100');
        // 添加评审事件和词条事件
        EntryReviewEvent::reviewEventAdd($review->id,$review->initiate_id,$review->initiater,'开启了评审计划<'.$review->title.'>。');
        // 词条添加热度记录
        $b_id = 24;
        EntryTemperatureRecord::recordAdd($review->eid,$review->initiate_id,$b_id,$createtime);
        // 获取词条母专业兴趣人员
        $interestUsers = Classification::where('id',$entry->cid)->first()->getInterestUsers()->pluck('user_id')->toArray();
        // 获取协作成员
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
        $users = array_merge($crewArr,$focusUsers);
        // 合并所有通知组用户
        $allUsers = array_unique(array_merge($interestUsers,$users));
        $usersToNotification = User::whereIn('id',$allUsers)->get();
        // 发送通知
        Notification::send($usersToNotification, new EntryReviewCreatedNotification($review));
    }
}
