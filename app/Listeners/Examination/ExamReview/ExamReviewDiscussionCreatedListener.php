<?php

namespace App\Listeners\Examination\ExamReview;

use App\Events\Examination\ExamReview\ExamReviewDiscussionCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Examination\ExamReview\ExamReviewDiscussionCreatedNotification;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\ExamReview;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamReview\ExamReviewEvent;
use App\Home\Examination\Exam\ExamDynamic;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamReviewDiscussionCreatedListener
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
     * @param  ExamReviewDiscussionCreatedEvent  $event
     * @return void
     */
    public function handle(ExamReviewDiscussionCreatedEvent $event)
    {
        //这里触发评审中立及支持事件，另外也触发评审的普通回复事件
        //中立票比较简单，投票成功后，通知相关用户即可
        $discussion = $event->examReviewDiscussion;
        $examReview = ExamReview::find($discussion->rid);
        $exam = Exam::find($examReview->exam_id);
        $cooperation = ExamCooperation::find($exam->cooperation_id);
        //发表了有效的评审意见后，积分和成长值+50
        User::expAndGrowValue($discussion->author_id,'50','50');
        // 添加评审事件
        if($discussion->standpoint == '1'){
            ExamReviewEvent::reviewEventAdd($discussion->rid,$discussion->author_id,$discussion->getAuthor->username,'支持本次评审计划通过。');
            $behavior = '发表评审计划支持意见：';
            $examBehavior = '新增评审计划支持意见';
        }elseif($discussion->standpoint == '3'){
            ExamReviewEvent::reviewEventAdd($discussion->rid,$discussion->author_id,$discussion->getAuthor->username,'投票并保持中立立场。');
            $behavior = '发表评审计划中立意见：';
            $examBehavior = '新增评审计划中立意见';
        }

        // 添加事件到用户动态
        $objectName = $examReview->title;
        $objectURL = '/examination/review/'.$exam->id.'/'.$exam->title.'#reviewDiscussion'.$discussion->id;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($discussion->author_id,$discussion->getAuthor->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        ExamDynamic::dynamicAdd($exam->id,$exam->title,$examBehavior,$objectName,$objectURL,$createtime);
        // 添加热度记录
        $b_id = 27;
        ExamTemperatureRecord::recordAdd($exam->id,$discussion->author_id,$b_id,$createtime);
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
        Notification::send($usersToNotification, new ExamReviewDiscussionCreatedNotification($discussion));
    }
}
