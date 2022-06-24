<?php

namespace App\Listeners\Examination\ExamDiscussion;

use App\Events\Examination\ExamDiscussion\ExamAdvisementRejectedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Examination\ExamDiscussion\ExamAdvisementRejectedToUserNotification;
use App\Notifications\Examination\ExamDiscussion\ExamAdvisementRejectedNotification;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamDiscussion\ExamDiscussionEvent;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamAdvisementRejectedListener
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
     * @param  ExamAdvisementRejectedEvent  $event
     * @return void
     */
    public function handle(ExamAdvisementRejectedEvent $event)
    {
        // 词条建议的讨论被拒绝后，通知原建议作者及协作小组
        $advise = $event->examAdvise;
        $exam = Exam::find($advise->exam_id);
        $cooperation = ExamCooperation::find($exam->cooperation_id);
        // 添加事件到用户动态
        $behavior = '拒绝了试卷建议讨论：';
        $objectName = $advise->title;
        $objectURL = '/examination/discussion/'.$exam->id.'/'.$exam->title.'#discussionAdvise'.$advise->id;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($advise->recipient_id,$advise->recipient,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        //反对被拒绝后，操作者的积分和成长值+20
        User::expAndGrowValue($advise->author_id,'100','100');
        // 添加讨论事件
        ExamDiscussionEvent::discussionEventAdd($exam->id,$advise->recipient_id,$advise->author,'拒绝了['.$advise->recipient.']提出的反对意见。');
        // 添加热度记录
        $b_id = 53;
        ExamTemperatureRecord::recordAdd($exam->id,$advise->recipient_id,$b_id,$createtime);
        // 给反对作者发送通知
        User::find($advise->author_id)->notify(new ExamAdvisementRejectedToUserNotification($advise));
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
        // 合并协作组与兴趣用户
        // $users = array_unique(array_merge($crewArr,$focusUsers));
        $users = array_unique($crewArr);
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyExamAdd($result));
        Notification::send($usersToNotification, new ExamAdvisementRejectedNotification($advise));
    }
}
