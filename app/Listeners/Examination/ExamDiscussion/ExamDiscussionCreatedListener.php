<?php

namespace App\Listeners\Examination\ExamDiscussion;

use App\Events\Examination\ExamDiscussion\ExamDiscussionCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Examination\ExamDiscussion\ExamDiscussionCreatedNotification;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamDiscussion\ExamDiscussionEvent;
use App\Home\Examination\Exam\ExamDynamic;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamDiscussionCreatedListener
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
     * @param  ExamDiscussionCreatedEvent  $event
     * @return void
     */
    public function handle(ExamDiscussionCreatedEvent $event)
    {
        //词条普通讨论创建后，仅通知协作组成员，不必通知其余用户
        $discussion = $event->examDiscussion;
        $exam = Exam::find($discussion->exam_id);
        $cooperation = ExamCooperation::find($exam->cooperation_id);
        // 添加事件到用户动态
        $behavior = '发表试卷普通讨论：';
        $objectName = $discussion->title;
        $objectURL = '/examination/discussion/'.$exam->id.'/'.$exam->title.'#discussion'.$discussion->id;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($discussion->author_id,$discussion->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $examBehavior = '新增普通讨论';
        ExamDynamic::dynamicAdd($exam->id,$exam->title,$examBehavior,$objectName,$objectURL,$createtime);
        //发表了有效的讨论后，积分和成长值+20
        User::expAndGrowValue($discussion->author_id,'20','20');
        // 添加热度记录
        $b_id = 54;
        ExamTemperatureRecord::recordAdd($exam->id,$discussion->author_id,$b_id,$createtime);
        // 添加讨论事件
        ExamDiscussionEvent::discussionEventAdd($exam->id,$discussion->author_id,$discussion->author,'发表了[普通]讨论内容：<'.$discussion->title.'>。');
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
        // $focusUsers = $Exam->ExamFocus()->pluck('user_id')->toArray();
        // 合并协作组与兴趣用户
        $users = array_unique($crewArr);
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyExamAdd($result));
        Notification::send($usersToNotification, new ExamDiscussionCreatedNotification($discussion));
    }
}
