<?php

namespace App\Listeners\Examination\ExamDebate\ExamDebateClosed;

use App\Events\Examination\ExamDebate\ExamDebateClosed\ExamDebateTimeOutClosedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Examination\ExamDebate\ExamDebateClosed\ExamDebateTimeOutClosedNotification;
use App\Notifications\Examination\ExamDebate\ExamDebateClosed\ExamDebateTimeOutClosedToUserNotification;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamDebate\ExamDebateEvent;
use App\Home\Examination\ExamReview\ExamReviewEvent;
use App\Home\Examination\ExamReview\ExamReviewOpponent;
use App\Home\Examination\ExamDiscussion\ExamDiscussionEvent;
use App\Home\Examination\ExamDiscussion\ExamOpponent;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\Classification;
use App\Home\Announcement;
use App\Home\Examination\ExamDynamic;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamDebateTimeOutClosedListener
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
     * @param  ExamDebateTimeOutClosedEvent  $event
     * @return void
     */
    public function handle(ExamDebateTimeOutClosedEvent $event)
    {
        $debate = $event->examDebate;
        $exam = Exam::find($debate->exam_id);
        $manage_id = $exam->manage_id;
        //超时分为攻方原因和辩方原因
        if($debate->status == '2'){
            // status为2，是由于攻方原因导致
            // 1、导致攻辩失败方扣除成长值1000
            User::expAndGrowValue($debate->Aauthor_id,0,-1000);
            // 2发布事件和公告
            // 3添加事件到辩论事件表
            ExamDebateEvent::debateEventAdd($debate->id,$debate->Aauthor_id,$debate->Aauthor,'攻辩:<'.$debate->title.'>由于攻方['.$debate->Aauthor.']回复超时关闭。');
            // 发表了有效的讨论后，积分和成长值+100
            // User::expAndGrowValue($debate->Aauthor_id,'100','100');
            // 4发布公告，1代表百科2代表，3代表辩论
            Announcement::announcementAdd('3','3','试卷《'.$exam->title.'》辩论<'.$debate->title.'>由于攻方回复超时已经关闭。','/examination/debate/'.$exam->id.'/'.$exam->title.'?type='.$debate->type.'&type_id='.$debate->type_id,$debate->updated_at);
            // 5添加事件到用户动态
            $behavior = '[攻方]回复超时导致结束攻辩：';
            $objectName = $debate->title;
            $objectURL = '/examination/debate/'.$exam->id.'/'.$exam->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
            $fromName = '试卷：'.$exam->title;
            $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
            $createtime = Carbon::now();
            UserDynamic::dynamicAdd($debate->Aauthor_id,$debate->Aauthor,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
            // 6添加事件到试卷动态
            $examBehavior = '攻辩超时关闭';
            ExamDynamic::dynamicAdd($exam->id,$exam->title,$examBehavior,$objectName,$objectURL,$createtime);
            // 添加热度记录
            $b_id = 64;
            ExamTemperatureRecord::recordAdd($exam->id,$debate->Aauthor_id,$b_id,$createtime);
            // 发布通知
            // 发送通知给辩论攻方接收人员
            User::find($debate->Bauthor_id)->notify(new ExamDebateTimeOutClosedToUserNotification($debate));
        }elseif($debate->status == '3'){
            // status为3，是由于辩方原因导致
            // 1、导致攻辩失败方扣除成长值1000
            User::expAndGrowValue($debate->Bauthor_id,0,-1000);
            // 2发布事件和公告
            // 3添加事件到辩论事件表
            ExamDebateEvent::debateEventAdd($debate->id,$debate->Bauthor_id,$debate->Bauthor,'攻辩:<'.$debate->title.'>由于辩方['.$debate->Bauthor.']回复超时关闭。');
            // 发表了有效的讨论后，积分和成长值+100
            // User::expAndGrowValue($debate->Aauthor_id,'100','100');
            // 4发布公告，1代表百科2代表，3代表辩论
            Announcement::announcementAdd('3','3','试卷《'.$exam->title.'》辩论<'.$debate->title.'>由于辩方回复超时已经关闭。','/home/examination/examDebate/'.$exam->id.'/'.$debate->type.'/'.$debate->type_id,$debate->created_at);
            // 5添加事件到用户动态
            $behavior = '[辩方]回复超时导致结束攻辩：';
            $objectName = $debate->title;
            $objectURL = '/home/examination/examDebate/'.$exam->id.'/'.$debate->type.'/'.$debate->type_id;
            $fromName = '试卷：'.$exam->title;
            $fromURL = '/home/examination/examDetail/'.$exam->id.'/'.$exam->title;
            $createtime = Carbon::now();
            UserDynamic::dynamicAdd($debate->Bauthor_id,$debate->Bauthor,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
            // 6添加事件到试卷动态
            $examBehavior = '攻辩超时关闭';
            ExamDynamic::dynamicAdd($exam->id,$exam->title,$examBehavior,$objectName,$objectURL,$createtime);
            // 添加热度记录
            $b_id = 65;
            ExamTemperatureRecord::recordAdd($exam->id,$debate->Bauthor_id,$b_id,$createtime);
            // 发布通知
            // 发送通知给辩论攻方接收人员
            User::find($debate->Bauthor_id)->notify(new ExamDebateTimeOutClosedToUserNotification($debate));
        }
        // 变更攻辩归口状态为攻辩失败
        if($debate->type == '1'){
            //添加事件到讨论事件表,$type==1表示该辩论来源是评审计划的反对
            $review_id = ExamReview::find(ExamReviewOpponent::find($debate->type_id)->rid);
            ExamReviewEvent::reviewEventAdd($review_id,$debate->Aauthor_id,$debate->Aauthor,'发起的攻辩:['.$debate->title.']已经失败[超时回复关闭]。');
            //改变反对状态为攻辩失败
            ExamReviewOpponent::where('id',$debate->type_id)->update([
                'status' => '5',
            ]);
        }elseif($debate->type == '2'){
            //添加事件到讨论事件表，$type==2是来源试卷讨论的反对
            ExamDiscussionEvent::discussionEventAdd($debate->exam_id,$debate->Aauthor_id,$debate->Aauthor,'发起的攻辩:['.$debate->title.']已经失败[超时回复关闭]。');
            //改变反对状态为攻辩失败
            ExamOpponent::where('id',$debate->type_id)->update([
                'status' => '5',
            ]);
        }
        // 获取试卷母专业兴趣人员
        $interestUsers = Classification::find($exam->cid)->getInterestUsers()->pluck('user_id')->toArray();
        // 获取协作成员
        array_push($interestUsers, $manage_id);
        // 获取试卷的关注用户
        $focusUsers = $exam->ExamFocus()->pluck('user_id')->toArray();
        // 合并所有通知组用户
        $allUsers = array_unique(array_merge($focusUsers,$interestUsers));
        $usersToNotification = User::whereIn('id',$allUsers)->get();
        // 发送通知
        Notification::send($usersToNotification, new ExamDebateTimeOutClosedNotification($debate));
    }
}
