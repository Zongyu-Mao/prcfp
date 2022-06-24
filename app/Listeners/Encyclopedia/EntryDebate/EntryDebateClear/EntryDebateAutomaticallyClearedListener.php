<?php

namespace App\Listeners\Encyclopedia\EntryDebate\EntryDebateClear;

use App\Events\Encyclopedia\EntryDebate\EntryDebateClear\EntryDebateAutomaticallyClearedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Encyclopedia\EntryDebate\EntryDebateClear\EntryDebateAutomaticallyClearedNotification;
use App\Notifications\Encyclopedia\EntryDebate\EntryDebateClear\EntryDebateAutomaticallyClearedToUserNotification;
use App\Home\Encyclopedia\EntryReview\EntryReviewOpponent;
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

class EntryDebateAutomaticallyClearedListener
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
     * @param  EntryDebateAutomaticallyClearedEvent  $event
     * @return void
     */
    public function handle(EntryDebateAutomaticallyClearedEvent $event)
    {
        //攻辩自动结算，写入事件，通知双方，公告，通知兴趣用户攻辩结束
        //词条辩论创建成功后，写入辩论事件，写入公告，通知所有与本词条有关的用户，通知辩论接收用户，写入相关事件
        $debate = $event->entryDebate;
        if($debate->type == '1'){
            //添加事件到讨论事件表,$type==1表示该辩论来源是评审计划的反对
            $review_id = EntryReview::find(EntryReviewOpponent::find($debate->type_id)->rid);
            EntryReviewEvent::reviewEventAdd($review_id,$debate->Aauthor_id,$debate->Aauthor,'向'.$debate->Bauthor.'发起的攻辩:['.$debate->title.']已经结算。');
            //改变反对状态为已转辩论
            EntryReviewOpponent::where('id',$debate->type_id)->update([
                'status' => '3',
            ]);
        }elseif($debate->type == '2'){
            //添加事件到讨论事件表，$type==2是来源词条讨论的反对
            EntryDiscussionEvent::discussionEventAdd($debate->eid,$debate->Aauthor_id,$debate->Aauthor,'向'.$debate->Bauthor.'发起的攻辩:['.$debate->title.']已经结算。');
            //改变反对状态为已转辩论
            EntryOpponent::where('id',$debate->type_id)->update([
                'status' => '3',
            ]);
        }
        $entry = Entry::find($debate->eid);
        $manage_id = $entry->manage_id;
        $createtime = Carbon::now();
        //添加事件到辩论事件表
        EntryDebateEvent::debateEventAdd($debate->id,$debate->Aauthor_id,$debate->Aauthor,'攻辩已经结束。');
        //计算双方的奖励和裁判工资
        $rewardA = $debate->ARedstars*5 - $debate->ABlackstars*10;
        $rewardB = $debate->BRedstars*5 - $debate->BBlackstars*10;
        if($debate->victory == '1'){
            $rewardA += '100';
            // 词条添加热度记录
            $b_id = 66;
            EntryTemperatureRecord::recordAdd($entry->id,$debate->Aauthor_id,$b_id,$createtime);
        }elseif($debate->victory == '2'){
            $rewardB += '100';
            // 词条添加热度记录
            $b_id = 67;
            EntryTemperatureRecord::recordAdd($entry->id,$debate->Bauthor_id,$b_id,$createtime);
        }
        User::expAndGrowValue($debate->Aauthor_id,$rewardA,$rewardA);
        User::expAndGrowValue($debate->Bauthor_id,$rewardB,$rewardB);
        // 发布公告，1代表百科，3代表辩论
        Announcement::announcementAdd('1','3','词条《'.$entry->title.'攻辩<'.$debate->title.'>已经结束[正常结算]。','/encyclopedia/debate/'.$entry->id.'/'.$entry->title.'?type='.$debate->type.'&type_id='.$debate->type_id,$debate->updated_at);
        // 攻辩结束不需要添加到用户动态
        $objectName = $debate->title;
        $objectURL = '/encyclopedia/debate/'.$entry->id.'/'.$entry->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        
        // 添加事件到词条动态
        $entryBehavior = '攻辩结束';
        EntryDynamic::dynamicAdd($entry->id,$entry->title,$entryBehavior,$objectName,$objectURL,$createtime);
        // 发布通知
        // 发送通知给辩论参与人员
        User::find($debate->Aauthor_id)->notify(new EntryDebateAutomaticallyClearedToUserNotification($debate));
        User::find($debate->Bauthor_id)->notify(new EntryDebateAutomaticallyClearedToUserNotification($debate));
        // 获取词条母专业兴趣人员
        $interestUsers = Classification::find($entry->cid)->getInterestUsers()->pluck('user_id')->toArray();
        // 获取协作成员
        array_push($interestUsers, $manage_id);
        // 获取词条的关注用户
        $focusUsers = $entry->entryFocus()->pluck('user_id')->toArray();
        $participateUsers = $debate->getStars->pluck('user_id')->toArray();
        $focusUsers = array_merge($focusUsers,$participateUsers);
        // 合并所有通知组用户
        $allUsers = array_unique(array_merge($focusUsers,$interestUsers));
        $usersToNotification = User::whereIn('id',$allUsers)->get();
        // 发送通知
        Notification::send($usersToNotification, new EntryDebateAutomaticallyClearedNotification($debate));
    }
}
