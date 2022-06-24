<?php

namespace App\Listeners\Examination\ExamReview;

use App\Events\Examination\ExamReview\ExamReviewOpponentAcceptedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Examination\ExamReview\ExamReviewOpponentAcceptedToUserNotification;
use App\Notifications\Examination\ExamReview\ExamReviewOpponentAcceptedNotification;
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

class ExamReviewOpponentAcceptedListener
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
     * @param  ExamReviewOpponentAcceptedEvent  $event
     * @return void
     */
    public function handle(ExamReviewOpponentAcceptedEvent $event)
    {
        //反对意见的接受，应通知协作组成员和原作者
        $opponent = $event->examReviewOpponent;
        $examReview = ExamReview::find($opponent->rid);
        $exam = Exam::find($examReview->exam_id);
        $cooperation = ExamCooperation::find($exam->cooperation_id);
        // 添加到用户动态
        $behavior = '接受了评审计划反对意见：';
        $objectName = $opponent->title;
        $objectURL = '/examination/review/'.$exam->id.'/'.$exam->title.'#reviewOpponent'.$opponent->id;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        UserDynamic::dynamicAdd($opponent->recipient_id,$opponent->recipient,$behavior,$objectName,$objectURL,$fromName,$fromURL,Carbon::now());
        //发表了有效的讨论后，积分和成长值+100
        User::expAndGrowValue($opponent->author_id,'100','100');
        // 添加讨论事件
        ExamReviewEvent::reviewEventAdd($examReview->id,$opponent->recipient_id,$opponent->recipient,'接受了['.$opponent->author.']提出的反对意见：<'.$opponent->title.'>。');
        // 通知原反对作者被拒绝
        User::find($opponent->author_id)->notify(new ExamReviewOpponentAcceptedToUserNotification($opponent));
        // 添加热度记录
        $b_id = 26;
        ExamTemperatureRecord::recordAdd($exam->id,$opponent->author_id,$b_id,$createtime);
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
        $users = array_unique($crewArr);
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyExamAdd($result));
        Notification::send($usersToNotification, new ExamReviewOpponentAcceptedNotification($opponent));
    }
}
