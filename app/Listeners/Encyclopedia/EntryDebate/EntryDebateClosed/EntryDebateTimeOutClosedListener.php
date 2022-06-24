<?php

namespace App\Listeners\Encyclopedia\EntryDebate\EntryDebateClosed;

use App\Events\Encyclopedia\EntryDebate\EntryDebateClosed\EntryDebateTimeOutClosedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Encyclopedia\EntryDebate\EntryDebateClosed\EntryDebateTimeOutClosedNotification;
use App\Notifications\Encyclopedia\EntryDebate\EntryDebateClosed\EntryDebateTimeOutClosedToUserNotification;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryDebate\EntryDebateEvent;
use App\Home\Encyclopedia\EntryReview;
use App\Home\Encyclopedia\EntryReview\EntryReviewEvent;
use App\Home\Encyclopedia\EntryReview\EntryReviewOpponent;
use App\Home\Encyclopedia\EntryDiscussion\EntryDiscussionEvent;
use App\Home\Encyclopedia\EntryDiscussion\EntryOpponent;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\Classification;
use App\Home\Announcement;
use App\Home\Encyclopedia\EntryDynamic;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryDebateTimeOutClosedListener
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
     * @param  EntryDebateTimeOutClosedEvent  $event
     * @return void
     */
    public function handle(EntryDebateTimeOutClosedEvent $event)
    {
        $debate = $event->entryDebate;
        $entry = Entry::find($debate->eid);
        $manage_id = $entry->manage_id;
        //超时分为攻方原因和辩方原因
        if($debate->status == '2'){
            // status为2，是由于攻方原因导致
            // 1、导致攻辩失败方扣除成长值1000
            User::expAndGrowValue($debate->Aauthor_id,0,-1000);
            // 2发布事件和公告
            // 3添加事件到辩论事件表
            EntryDebateEvent::debateEventAdd($debate->id,$debate->Aauthor_id,$debate->Aauthor,'攻辩:<'.$debate->title.'>由于攻方['.$debate->Aauthor.']回复超时关闭。');
            // 发表了有效的讨论后，积分和成长值+100
            // User::expAndGrowValue($debate->Aauthor_id,'100','100');
            // 4发布公告，1代表百科，3代表辩论
            Announcement::announcementAdd('1','3','词条《'.$entry->title.'》辩论<'.$debate->title.'>由于攻方回复超时已经关闭。','/encyclopedia/debate/'.$entry->id.'/'.$entry->title.'?type='.$debate->type.'&type_id='.$debate->type_id,$debate->updated_at);
            // 5添加事件到用户动态
            $behavior = '[攻方]回复超时导致结束攻辩：';
            $objectName = $debate->title;
            $objectURL = '/encyclopedia/debate/'.$entry->id.'/'.$entry->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
            $fromName = '词条：'.$entry->title;
            $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
            $createtime = Carbon::now();
            UserDynamic::dynamicAdd($debate->Bauthor_id,$debate->Bauthor,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
            // 6添加事件到词条动态
            $entryBehavior = '攻辩超时关闭';
            EntryDynamic::dynamicAdd($entry->id,$entry->title,$entryBehavior,$objectName,$objectURL,$createtime);
            // 添加词条热度
            // 这里暂时按放弃处理，如果要变更的再说，还要考虑莫名结束对裁判及攻辩整个的影响*********************************************************
            $b_id = 64;
            EntryTemperatureRecord::recordAdd($entry->id,$debate->Aauthor_id,$b_id,$createtime);
            // 发布通知
            // 发送通知给辩论攻方接收人员
            User::find($debate->Bauthor_id)->notify(new EntryDebateTimeOutClosedToUserNotification($debate));
        }elseif($debate->status == '3'){
            // status为3，是由于辩方原因导致
            // 1、导致攻辩失败方扣除成长值1000
            User::expAndGrowValue($debate->Bauthor_id,0,-1000);
            // 2发布事件和公告
            // 3添加事件到辩论事件表
            EntryDebateEvent::debateEventAdd($debate->id,$debate->Bauthor_id,$debate->Bauthor,'攻辩:<'.$debate->title.'>由于辩方['.$debate->Bauthor.']回复超时关闭。');
            // 发表了有效的讨论后，积分和成长值+100
            // User::expAndGrowValue($debate->Aauthor_id,'100','100');
            // 4发布公告，1代表百科，3代表辩论
            Announcement::announcementAdd('1','3','词条《'.$entry->title.'》辩论<'.$debate->title.'>由于辩方回复超时已经关闭。','/encyclopedia/debate/'.$entry->id.'/'.$entry->title.'?type='.$debate->type.'&type_id='.$debate->type_id,$debate->created_at);
            // 5添加事件到用户动态
            $behavior = '[辩方]回复超时导致结束攻辩：';
            $objectName = $debate->title;
            $objectURL = '/encyclopedia/debate/'.$entry->id.'/'.$entry->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
            $fromName = '词条：'.$entry->title;
            $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
            $createtime = Carbon::now();
            UserDynamic::dynamicAdd($debate->Bauthor_id,$debate->Bauthor,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
            // 6添加事件到词条动态
            $entryBehavior = '攻辩超时关闭';
            EntryDynamic::dynamicAdd($entry->id,$entry->title,$entryBehavior,$objectName,$objectURL,$createtime);
            // 添加词条热度
            $b_id = 65;
            EntryTemperatureRecord::recordAdd($entry->id,$debate->Bauthor_id,$b_id,$createtime);
            // 发布通知
            // 发送通知给辩论攻方接收人员
            User::find($debate->Bauthor_id)->notify(new EntryDebateTimeOutClosedToUserNotification($debate));
        }
        // 变更攻辩归口状态为攻辩失败
        if($debate->type == '1'){
            //添加事件到讨论事件表,$type==1表示该辩论来源是评审计划的反对
            $review_id = EntryReviewOpponent::find($debate->type_id)->rid;
            EntryReviewEvent::reviewEventAdd($review_id,$debate->Aauthor_id,$debate->Aauthor,'发起的攻辩:['.$debate->title.']已经失败[超时回复关闭]。');
            //改变反对状态为攻辩失败
            EntryReviewOpponent::where('id',$debate->type_id)->update([
                'status' => '5',
            ]);
        }elseif($debate->type == '2'){
            //添加事件到讨论事件表，$type==2是来源词条讨论的反对
            EntryDiscussionEvent::discussionEventAdd($debate->eid,$debate->Aauthor_id,$debate->Aauthor,'发起的攻辩:['.$debate->title.']已经失败[超时回复关闭]。');
            //改变反对状态为攻辩失败
            EntryOpponent::where('id',$debate->type_id)->update([
                'status' => '5',
            ]);
        }
        // 获取词条母专业兴趣人员
        $interestUsers = Classification::find($entry->cid)->getInterestUsers()->pluck('user_id')->toArray();
        // 获取协作成员
        array_push($interestUsers, $manage_id);
        // 获取词条的关注用户
        $focusUsers = $entry->entryFocus()->pluck('user_id')->toArray();
        // 合并所有通知组用户
        $allUsers = array_unique(array_merge($focusUsers,$interestUsers));
        $usersToNotification = User::whereIn('id',$allUsers)->get();
        // 发送通知
        Notification::send($usersToNotification, new EntryDebateTimeOutClosedNotification($debate));
    }
}
