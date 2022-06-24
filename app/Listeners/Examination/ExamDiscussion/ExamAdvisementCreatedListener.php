<?php

namespace App\Listeners\Examination\ExamDiscussion;

use App\Events\Examination\ExamDiscussion\ExamAdvisementCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Examination\ExamDiscussion\ExamAdvisementCreatedNotification;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamDiscussion\ExamDiscussionEvent;
use App\Home\Examination\Exam\ExamDynamic;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamAdvisementCreatedListener
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
     * @param  ExamAdvisementCreatedEvent  $event
     * @return void
     */
    public function handle(ExamAdvisementCreatedEvent $event)
    {
        //提出建议后，仍然通知协作成员
        $advise = $event->examAdvise;
        $exam = Exam::find($advise->exam_id);
        $cooperation = ExamCooperation::find($exam->cooperation_id);
        // 添加事件到用户动态
        $behavior = '发表了试卷建议讨论：';
        $objectName = $advise->title;
        $objectURL = '/examination/discussion/'.$exam->id.'/'.$exam->title.'#discussionAdvise'.$advise->id;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($advise->author_id,$advise->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $examBehavior = '新增建议讨论';
        ExamDynamic::dynamicAdd($exam->id,$exam->title,$examBehavior,$objectName,$objectURL,$createtime);
        //发表了有效的讨论后，积分和成长值+100
        User::expAndGrowValue($advise->author_id,'50','50');
        // 添加热度记录
        $b_id = 51;
        ExamTemperatureRecord::recordAdd($exam->id,$advise->author_id,$b_id,$createtime);
        // 添加讨论事件
        ExamDiscussionEvent::discussionEventAdd($exam->id,$advise->author_id,$advise->author,'发表了立场为[建议]的讨论内容：<'.$advise->title.'>。');
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
        Notification::send($usersToNotification, new ExamAdvisementCreatedNotification($advise));
    }
}
