<?php

namespace App\Listeners\Examination\ExamDiscussion;

use App\Events\Examination\ExamDiscussion\ExamOpponentCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Examination\ExamDiscussion\ExamOpponentCreatedNotification;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\ExamReview;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamDiscussion\ExamDiscussionEvent;
use App\Home\Examination\Exam\ExamDynamic;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamOpponentCreatedListener
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
     * @param  ExamOpponentCreatedEvent  $event
     * @return void
     */
    public function handle(ExamOpponentCreatedEvent $event)
    {
        //反对的讨论创建后，仅通知协作组成员和关注词条用户，不必通知关注该分类的用户
        $opponent = $event->examOpponent;
        $exam = Exam::find($opponent->exam_id);
        $cooperation = ExamCooperation::find($exam->cooperation_id);
        // 添加事件到用户动态
        $behavior = '发表了试卷反对讨论：';
        $objectName = $opponent->title;
        $objectURL = '/examination/discussion/'.$exam->id.'/'.$exam->title.'#discussionOpponent'.$opponent->id;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($opponent->author_id,$opponent->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $examBehavior = '新增反对讨论';
        ExamDynamic::dynamicAdd($exam->id,$exam->title,$examBehavior,$objectName,$objectURL,$createtime);
        //发表了有效的讨论后，积分和成长值+100
        User::expAndGrowValue($opponent->author_id,'100','100');
        // 添加热度记录
        $b_id = 48;
        ExamTemperatureRecord::recordAdd($exam->id,$opponent->author_id,$b_id,$createtime);
        // 添加讨论事件
        ExamDiscussionEvent::discussionEventAdd($exam->id,$opponent->author_id,$opponent->author,'发表了立场为[反对]的讨论内容：<'.$opponent->title.'>。');
        // 开启对协作组成员和关注词条用户的通知
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
        $focusUsers = $exam->examFocus()->pluck('user_id')->toArray();
        // 合并协作组与兴趣用户
        $users = array_unique(array_merge($crewArr,$focusUsers));
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyExamAdd($result));
        Notification::send($usersToNotification, new ExamOpponentCreatedNotification($opponent));
    }
}
