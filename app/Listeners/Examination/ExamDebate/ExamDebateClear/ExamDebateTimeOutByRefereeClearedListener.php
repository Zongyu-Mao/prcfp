<?php

namespace App\Listeners\Examination\ExamDebate\ExamDebateClear;

use App\Events\Examination\ExamDebate\ExamDebateClear\ExamDebateTimeOutByRefereeClearedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Examination\ExamDebate\ExamDebateClear\ExamDebateTimeOutByRefereeClearedNotification;
use App\Notifications\Examination\ExamDebate\ExamDebateClear\ExamDebateTimeOutByRefereeClearedToUserNotification;
use App\Notifications\Examination\ExamDebate\ExamDebateClear\ExamDebateTimeOutByRefereeClearedToRefereeNotification;
use App\Home\Examination\ExamReview\ExamReviewOpponent;
use App\Home\Examination\ExamReview\ExamReviewEvent;
use App\Home\Examination\ExamDiscussion\ExamDiscussionEvent;
use App\Home\Examination\ExamDiscussion\ExamOpponent;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamDebate\ExamDebateEvent;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\Classification;
use App\Home\Announcement;
use App\Home\Examination\ExamDynamic;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamDebateTimeOutByRefereeClearedListener
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
     * @param  ExamDebateTimeOutByRefereeClearedEvent  $event
     * @return void
     */
    public function handle(ExamDebateTimeOutByRefereeClearedEvent $event)
    {
        // 裁判超时，对裁判惩罚
        //攻辩自动结算，写入事件，通知双方，公告，通知兴趣用户攻辩结束
        //试卷辩论创建成功后，写入辩论事件，写入公告，通知所有与本试卷有关的用户，通知辩论接收用户，写入相关事件
        $debate = $event->examDebate;
        if($debate->type == '1'){
            //添加事件到讨论事件表,$type==1表示该辩论来源是评审计划的反对
            $review_id = ExamReview::find(ExamReviewOpponent::find($debate->type_id)->rid);
            ExamReviewEvent::reviewEventAdd($review_id,$debate->Aauthor_id,$debate->Aauthor,'向'.$debate->Bauthor.'发起的攻辩:['.$debate->title.']已经结算。');
            //改变反对状态为已转辩论
            ExamReviewOpponent::where('id',$debate->type_id)->update([
                'status' => '3',
            ]);
        }elseif($debate->type == '2'){
            //添加事件到讨论事件表，$type==2是来源试卷讨论的反对
            ExamDiscussionEvent::discussionEventAdd($debate->exam_id,$debate->Aauthor_id,$debate->Aauthor,'向'.$debate->Bauthor.'发起的攻辩:['.$debate->title.']已经结算。');
            //改变反对状态为已转辩论
            ExamOpponent::where('id',$debate->type_id)->update([
                'status' => '3',
            ]);
        }
        $exam = Exam::find($debate->exam_id);
        $manage_id = $exam->manage_id;
        //添加事件到辩论事件表
        ExamDebateEvent::debateEventAdd($debate->id,$debate->Aauthor_id,$debate->Aauthor,'攻辩已经结束（裁判结算超时，自动结算）。');
        //计算双方的奖励和裁判工资
        $rewardA = $debate->ARedstars*5 - $debate->ABlackstars*10;
        $rewardB = $debate->BRedstars*5 - $debate->BBlackstars*10;
        $rewardR = $debate->RRedstars*5 - $debate->RBlackstars*10-1000;
        // 补充对裁判的不诚信记录**************************************************************************
        if($debate->victory == '1'){
            $rewardA += '100';
            $creator = $debate->Aauthor;
            $creator_id = $debate->Aauthor_id;
            // 添加热度记录
            $b_id = 66;
            ExamTemperatureRecord::recordAdd($exam->id,$debate->Aauthor_id,$b_id,$createtime);
        }elseif($debate->victory == '2'){
            $rewardB += '100';
            $creator = $debate->Bauthor;
            $creator_id = $debate->Bauthor_id;
            // 添加热度记录
            $b_id = 67;
            ExamTemperatureRecord::recordAdd($exam->id,$debate->Bauthor_id,$b_id,$createtime);
        }
        User::expAndGrowValue($debate->Aauthor_id,$rewardA,$rewardA);
        User::expAndGrowValue($debate->Bauthor_id,$rewardB,$rewardB);
        User::expAndGrowValue($debate->referee_id,$rewardR,$rewardR);
        // 发布公告，1代表百科2代表著作，3代表辩论
        Announcement::announcementAdd('2','3','著作《'.$exam->title.'攻辩<'.$debate->title.'>已经结束[裁判结算超时，自动结算]。','/home/examination/examDebate/'.$exam->id.'/'.$debate->type.'/'.$debate->type_id,$debate->updated_at);
        // 攻辩结束不需要添加到用户动态
        $objectName = $debate->title;
        $objectURL = '/home/examination/examDebate/'.$exam->id.'/'.$debate->type.'/'.$debate->type_id;
        $createtime = Carbon::now();
        // 添加事件到试卷动态
        $examBehavior = '攻辩结束';
        ExamDynamic::dynamicAdd($exam->id,$exam->title,$examBehavior,$objectName,$objectURL,$createtime);
        // 添加热度记录,裁判误时
        $b_id = 68;
        ExamTemperatureRecord::recordAdd($exam->id,$debate->referee_id,$b_id,$createtime);
        // 发布通知
        // 发送通知给辩论参与人员
        User::find($debate->Aauthor_id)->notify(new ExamDebateTimeOutByRefereeClearedToUserNotification($debate));
        User::find($debate->Bauthor_id)->notify(new ExamDebateTimeOutByRefereeClearedToUserNotification($debate));
        User::find($debate->referee_id)->notify(new ExamDebateTimeOutByRefereeClearedToRefereeNotification($debate));
        // 获取试卷母专业兴趣人员
        $interestUsers = Classification::find($exam->cid)->getInterestUsers()->pluck('user_id')->toArray();
        // 获取协作成员
        array_push($interestUsers, $manage_id);
        // 获取试卷的关注用户
        $focusUsers = $exam->examFocus()->pluck('user_id')->toArray();
        $participateUsers = $debate->getStars->pluck('user_id')->toArray();
        $focusUsers = array_merge($focusUsers,$participateUsers);
        // 合并所有通知组用户
        $allUsers = array_unique(array_merge($focusUsers,$interestUsers));
        $usersToNotification = User::whereIn('id',$allUsers)->get();
        // 发送通知
        Notification::send($usersToNotification, new ExamDebateTimeOutByRefereeClearedNotification($debate));
    }
}
