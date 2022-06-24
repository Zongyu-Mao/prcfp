<?php

namespace App\Listeners\Examination\ExamReview;

use App\Events\Examination\ExamReview\ExamReviewAdvisementRejectedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Examination\ExamReview\ExamReviewAdvisementRejectedToUserNotification;
use App\Notifications\Examination\ExamReview\ExamReviewAdvisementRejectedNotification;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\ExamReview;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamReview\ExamReviewAdvise;
use App\Home\Examination\ExamReview\ExamReviewEvent;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamReviewAdvisementRejectedListener
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
     * @param  ExamReviewAdvisementRejectedEvent  $event
     * @return void
     */
    public function handle(ExamReviewAdvisementRejectedEvent $event)
    {
        //评审计划建议的拒绝，应通知协作小组成员和原作者
        $advise = $event->examReviewAdvise;
        $examReview = ExamReview::find($advise->rid);
        $parentReviewAdvise = ExamReviewAdvise::find($advise->pid);
        $exam = Exam::find($examReview->exam_id);
        $cooperation = ExamCooperation::find($exam->cooperation_id);
        // 添加事件到用户动态
        $behavior = '拒绝评审计划建议：';
        $objectName = $advise->title;
        $objectURL = '/examination/review/'.$exam->id.'/'.$exam->title.'#reviewAdvise'.$advise->id;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($advise->author_id,$advise->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        //发表了有效的讨论后，积分和成长值+100
        User::expAndGrowValue($advise->author_id,'100','100');
        // 添加讨论事件
        ExamReviewEvent::reviewEventAdd($examReview->id,$advise->author_id,$advise->author,'拒绝了['.$advise->recipient.']提出的建议：<'.$parentReviewAdvise->title.'>，理由：<'.$advise->title.'>。');
        // 添加热度记录
        $b_id = 30;
        ExamTemperatureRecord::recordAdd($exam->id,$advise->recipient_id,$b_id,$createtime);
        // 通知原反对作者被拒绝
        User::find($advise->recipient_id)->notify(new ExamReviewAdvisementRejectedToUserNotification($advise));
        // 开启对协作组成员的通知
        $manage_id = $exam->manage_id;
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
        // $focusUsers = $Exam->ExamFocus()->pluck('user_id')->toArray();
        // 合并协作组
        $users = array_unique($crewArr);
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyExamAdd($result));
        Notification::send($usersToNotification, new ExamReviewAdvisementRejectedNotification($advise));
    }
}
