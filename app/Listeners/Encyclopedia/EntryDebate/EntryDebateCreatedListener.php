<?php

namespace App\Listeners\Encyclopedia\EntryDebate;

use App\Events\Encyclopedia\EntryDebate\EntryDebateCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Encyclopedia\EntryDebate\EntryDebateCreatedNotification;
use App\Notifications\Encyclopedia\EntryDebate\EntryDebateCreatedToUserNotification;
use App\Home\Encyclopedia\EntryReview\EntryReviewOpponent;
use App\Home\Encyclopedia\EntryReview;
use App\Home\Encyclopedia\EntryReview\EntryReviewEvent;
use App\Home\Encyclopedia\EntryDiscussion\EntryDiscussionEvent;
use App\Home\Encyclopedia\EntryDiscussion\EntryOpponent;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryDebate\EntryDebateEvent;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\Classification;
use App\Home\Announcement;
use App\Home\Encyclopedia\EntryDynamic;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryDebateCreatedListener
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
     * @param  EntryDebateCreatedEvent  $event
     * @return void
     */
    public function handle(EntryDebateCreatedEvent $event)
    {
        //词条辩论创建成功后，写入辩论事件，写入公告，通知所有与本词条有关的用户，通知辩论接收用户，写入相关事件
        $debate = $event->entryDebate;
        if($debate->type == '1'){
            //添加事件到讨论事件表,$type==1表示该辩论来源是评审计划的反对
            $review_id = EntryReviewOpponent::find($debate->type_id)->rid;
            EntryReviewEvent::reviewEventAdd($review_id,$debate->Aauthor_id,$debate->Aauthor,'向'.$debate->Bauthor.'发起了攻辩:['.$debate->title.']。');
            //改变反对状态为已转辩论
            EntryReviewOpponent::where('id',$debate->type_id)->update([
                'recipient' => $debate->Aauthor,
                'recipient_id' => $debate->Aauthor_id,
                'status' => '4'
            ]);
        }elseif($debate->type == '2'){
            //添加事件到讨论事件表，$type==2是来源词条讨论的反对
            EntryDiscussionEvent::discussionEventAdd($debate->eid,$debate->Aauthor_id,$debate->Aauthor,'向'.$debate->Bauthor.'发起了攻辩:['.$debate->title.']。');
            //改变反对状态为已转辩论
            EntryOpponent::where('id',$debate->type_id)->update([
                'recipient' => $debate->Aauthor,
                'recipient_id' => $debate->Aauthor_id,
                'status' => '4'
            ]);
        }
        $entry = Entry::find($debate->eid);
        $manage_id = $entry->manage_id;
        $createtime = Carbon::now();
        //添加事件到辩论事件表
        EntryDebateEvent::debateEventAdd($debate->id,$debate->Aauthor_id,$debate->Aauthor,'向'.$debate->Bauthor.'发起了攻辩:<'.$debate->title.'>。');
        // 词条添加热度记录
        $b_id = 59;
        EntryTemperatureRecord::recordAdd($entry->id,$debate->Aauthor_id,$b_id,$createtime);
        //发表了有效的讨论后，积分和成长值+100
        User::expAndGrowValue($debate->Aauthor_id,'100','100');
        // 发布公告，1代表百科，3代表辩论
        Announcement::announcementAdd('1','3','词条《'.$entry->title.'》新增辩论<'.$debate->title.'>。','/encyclopedia/debate/'.$entry->id.'/'.$entry->title.'?type='.$debate->type.'&type_id='.$debate->type_id,$debate->created_at);
        // 添加事件到用户动态
        $behavior = '发起了攻辩：';
        $objectName = $debate->title;
        $objectURL = '/encyclopedia/debate/'.$entry->id.'/'.$entry->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        
        UserDynamic::dynamicAdd($debate->Aauthor_id,$debate->Aauthor,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $entryBehavior = '新增攻辩';
        EntryDynamic::dynamicAdd($entry->id,$entry->title,$entryBehavior,$objectName,$objectURL,$createtime);
        // 发布通知
        // 发送通知给辩论接收人员
        User::find($debate->Bauthor_id)->notify(new EntryDebateCreatedToUserNotification($debate));
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
        Notification::send($usersToNotification, new EntryDebateCreatedNotification($debate));
    }
}
