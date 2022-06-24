<?php

namespace App\Listeners\Examination\ExamDiscussion;

use App\Events\Examination\ExamDiscussion\ExamOpponentAcceptedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Examination\ExamDiscussion\ExamOpponentAcceptedNotification;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamDiscussion\ExamDiscussionEvent;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamOpponentAcceptedListener
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
     * @param  ExamOpponentAcceptedEvent  $event
     * @return void
     */
    public function handle(ExamOpponentAcceptedEvent $event)
    {
        // 反对的讨论被接受后，仅通知讨论的作者
        $opponent = $event->examOpponent;
        $exam = Exam::find($opponent->exam_id);
        //反对被接受后，作者的积分和成长值+20
        User::expAndGrowValue($opponent->author_id,'20','20');
        //反对被接受后，操作者的积分和成长值+20
        User::expAndGrowValue($opponent->recipient_id,'20','20');
        // 添加讨论事件
        ExamDiscussionEvent::discussionEventAdd($exam->id,$opponent->recipient_id,$opponent->recipient,'接受了['.$opponent->author.']提出的反对意见。');
        // 添加事件到用户动态
        $behavior = '接受了试卷反对讨论：';
        $objectName = $opponent->title;
        $objectURL = '/examination/discussion/'.$exam->id.'/'.$exam->title.'#discussionOpponent'.$opponent->id;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($opponent->recipient_id,$opponent->recipient,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加热度记录
        $b_id = 49;
        ExamTemperatureRecord::recordAdd($exam->id,$opponent->recipient_id,$b_id,$createtime);
        // 给反对作者发送通知
        User::find($opponent->author_id)->notify(new ExamOpponentAcceptedNotification($opponent));
    }
}
