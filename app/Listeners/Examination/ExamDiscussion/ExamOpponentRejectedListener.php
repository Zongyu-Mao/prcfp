<?php

namespace App\Listeners\Examination\ExamDiscussion;

use App\Events\Examination\ExamDiscussion\ExamOpponentRejectedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Examination\ExamDiscussion\ExamOpponentRejectedToUserNotification;
use App\Notifications\Examination\ExamDiscussion\ExamOpponentRejectedNotification;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamDiscussion\ExamDiscussionEvent;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamOpponentRejectedListener
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
     * @param  ExamOpponentRejectedEvent  $event
     * @return void
     */
    public function handle(ExamOpponentRejectedEvent $event)
    {
        // 反对的讨论被拒绝后，原则上应通知所有相关用户，由于拒绝评论是新建的，因此原讨论的接受者与作者身份互换
        $opponent = $event->examOpponent;
        $exam = Exam::find($opponent->exam_id);
        $cooperation = ExamCooperation::find($exam->cooperation_id);
        //反对被拒绝后，操作者的积分和成长值+20
        User::expAndGrowValue($opponent->author_id,'100','100');
        // 添加讨论事件
        ExamDiscussionEvent::discussionEventAdd($exam->id,$opponent->author_id,$opponent->author,'拒绝了['.$opponent->recipient.']提出的反对意见。');
        // 添加事件到用户动态
        $behavior = '拒绝了试卷反对讨论：';
        $objectName = $opponent->title;
        $objectURL = '/examination/discussion/'.$exam->id.'/'.$exam->title.'#discussionOpponent'.$opponent->id;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($opponent->recipient_id,$opponent->recipient,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加热度记录
        $b_id = 50;
        ExamTemperatureRecord::recordAdd($exam->id,$opponent->recipient_id,$b_id,$createtime);
        // 给反对作者发送通知
        User::find($opponent->recipient_id)->notify(new ExamOpponentRejectedToUserNotification($opponent));
        // 通知词条相关用户
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
        $focusUsers = $exam->ExamFocus()->pluck('user_id')->toArray();
        // 合并协作组与兴趣用户
        $users = array_unique(array_merge($crewArr,$focusUsers));
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyExamAdd($result));
        Notification::send($usersToNotification, new ExamOpponentRejectedNotification($opponent));
    }
}
