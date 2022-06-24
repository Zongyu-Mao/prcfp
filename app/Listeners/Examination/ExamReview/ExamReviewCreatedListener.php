<?php

namespace App\Listeners\Examination\ExamReview;

use App\Events\Examination\ExamReview\ExamReviewCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Examination\ExamReview\ExamReviewCreatedNotification;
use App\Home\Examination\ExamReview\ExamReviewEvent;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\Exam;
use App\Home\Announcement;
use App\Home\Classification;
use App\Home\Examination\Exam\ExamDynamic;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamReviewCreatedListener
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
     * @param  ExamReviewCreatedEvent  $event
     * @return void
     */
    public function handle(ExamReviewCreatedEvent $event)
    {
        //评审计划创建后，通知协作组成员和词条关注用户评审计划创建成功

        $review = $event->examReview;
        $exam = Exam::find($review->exam_id);
        $cooperation = ExamCooperation::find($exam->cooperation_id);
        // 添加事件到用户动态
        $behavior = '开启评审计划：';
        $objectName = $review->title;
        $objectURL = '/examination/review/'.$exam->id.'/'.$exam->title;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($review->initiate_id,$review->initiater,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $examBehavior = '开启评审计划';
        ExamDynamic::dynamicAdd($exam->id,$exam->title,$examBehavior,$objectName,$objectURL,$createtime);
        // 发布公告，2代表著作，2代表评审计划,3代表试卷
        Announcement::announcementAdd('3','2','试卷《'.$exam->title.'》的评审计划<'.$review->title.'>已经创建。','/examination/review/'.$exam->id.'/'.$exam->title,$review->created_at);
        // Exam::where('id',$review->exam_id)->update(['review_id' => $review->id]);
        // 增加用户积分
        User::expAndGrowValue($review->initiate_id,'100','100');
        // 添加评审事件和词条事件
        ExamReviewEvent::reviewEventAdd($review->id,$review->initiate_id,$review->initiater,'开启了评审计划<'.$review->title.'>。');
        // 添加热度记录
        $b_id = 24;
        ExamTemperatureRecord::recordAdd($review->exam_id,$review->initiate_id,$b_id,$createtime);
        // 获取词条母专业兴趣人员
        $interestUsers = Classification::where('id',$exam->cid)->first()->getInterestUsers()->pluck('user_id')->toArray();
        // 获取协作成员
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
        $users = array_merge($crewArr,$focusUsers);
        // 合并所有通知组用户
        $allUsers = array_unique(array_merge($interestUsers,$users));
        $usersToNotification = User::whereIn('id',$allUsers)->get();
        // 发送通知
        Notification::send($usersToNotification, new ExamReviewCreatedNotification($review));
    }
}
