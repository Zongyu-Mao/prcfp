<?php

namespace App\Listeners\Examination\ExamDebate;

use App\Events\Examination\ExamDebate\ExamDebateCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Examination\ExamDebate\ExamDebateCreatedNotification;
use App\Notifications\Examination\ExamDebate\ExamDebateCreatedToUserNotification;
use App\Home\Examination\ExamReview;
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
use App\Home\Examination\Exam\ExamDynamic;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamDebateCreatedListener
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
     * @param  ExamDebateCreatedEvent  $event
     * @return void
     */
    public function handle(ExamDebateCreatedEvent $event)
    {
        //辩论创建成功后，写入辩论事件，写入公告，通知所有与本词条有关的用户，通知辩论接收用户，写入相关事件
        $debate = $event->examDebate;
        if($debate->type == 1){
            //添加事件到讨论事件表,$type==1表示该辩论来源是评审计划的反对
            $review_id = ExamReviewOpponent::find($debate->type_id)->rid;
            ExamReviewEvent::reviewEventAdd($review_id,$debate->Aauthor_id,$debate->Aauthor,'向'.$debate->Bauthor.'发起了攻辩:['.$debate->title.']。');
            //改变反对状态为已转辩论
            ExamReviewOpponent::where('id',$debate->type_id)->update([
                'status' => '4',
            ]);
        }elseif($debate->type == '2'){
            //添加事件到讨论事件表，$type==2是来源词条讨论的反对
            ExamDiscussionEvent::discussionEventAdd($debate->exam_id,$debate->Aauthor_id,$debate->Aauthor,'向'.$debate->Bauthor.'发起了攻辩:['.$debate->title.']。');
            //改变反对状态为已转辩论
            ExamOpponent::where('id',$debate->type_id)->update([
                'status' => '4',
            ]);
        }
        $exam = Exam::find($debate->exam_id);
        $manage_id = $exam->manage_id;
        //添加事件到辩论事件表
        ExamDebateEvent::debateEventAdd($debate->id,$debate->Aauthor_id,$debate->Aauthor,'向'.$debate->Bauthor.'发起了攻辩:<'.$debate->title.'>。');
        //发表了有效的讨论后，积分和成长值+100
        User::expAndGrowValue($debate->Aauthor_id,'100','100');
        // 发布公告，1代表百科2代表著作，3代表辩论
        Announcement::announcementAdd('3','3','著作《'.$exam->title.'》新增辩论<'.$debate->title.'>。','/examination/debate/'.$exam->id.'/'.$exam->title.'?type='.$debate->type.'&type_id='.$debate->type_id,$debate->created_at);
        // 添加事件到用户动态
        $behavior = '发起了攻辩：';
        $objectName = $debate->title;
        $objectURL = '/examination/debate/'.$exam->id.'/'.$debate->type.'/'.$debate->type_id;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($debate->Aauthor_id,$debate->Aauthor,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到动态
        $examBehavior = '新增攻辩';
        ExamDynamic::dynamicAdd($exam->id,$exam->title,$examBehavior,$objectName,$objectURL,$createtime);
        // 添加热度记录
        $b_id = 59;
        ExamTemperatureRecord::recordAdd($exam->id,$debate->Aauthor_id,$b_id,$createtime);
        // 发布通知
        // 发送通知给辩论接收人员
        User::find($debate->Bauthor_id)->notify(new ExamDebateCreatedToUserNotification($debate));
        // 获取词条母专业兴趣人员
        $interestUsers = Classification::find($exam->cid)->getInterestUsers()->pluck('user_id')->toArray();
        // 获取协作成员
        array_push($interestUsers, $manage_id);
        // 获取词条的关注用户
        $focusUsers = $exam->ExamFocus()->pluck('user_id')->toArray();
        // 合并所有通知组用户
        $allUsers = array_unique(array_merge($focusUsers,$interestUsers));
        $usersToNotification = User::whereIn('id',$allUsers)->get();
        // 发送通知
        Notification::send($usersToNotification, new ExamDebateCreatedNotification($debate));
    }
}
