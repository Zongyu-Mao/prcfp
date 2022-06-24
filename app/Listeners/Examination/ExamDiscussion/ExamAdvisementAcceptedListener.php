<?php

namespace App\Listeners\Examination\ExamDiscussion;

use App\Events\Examination\ExamDiscussion\ExamAdvisementAcceptedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Examination\ExamDiscussion\ExamAdvisementAcceptedNotification;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamDiscussion\ExamDiscussionEvent;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamAdvisementAcceptedListener
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
     * @param  ExamAdvisementAcceptedEvent  $event
     * @return void
     */
    public function handle(ExamAdvisementAcceptedEvent $event)
    {
        // 词条反对的讨论被接受后，仅通知讨论的作者
        $advise = $event->examAdvise;
        $exam = Exam::find($advise->exam_id);
        // 添加事件到用户动态
        $behavior = '接受了试卷建议讨论：';
        $objectName = $advise->title;
        $objectURL = '/examination/discussion/'.$exam->id.'/'.$exam->title.'#discussionAdvise'.$advise->id;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($advise->recipient_id,$advise->recipient,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        //建议被接受后，作者的积分和成长值+20
        User::expAndGrowValue($advise->author_id,'20','20');
        //建议被接受后，操作者的积分和成长值+20
        User::expAndGrowValue($advise->recipient_id,'20','20');
        // 添加讨论事件
        ExamDiscussionEvent::discussionEventAdd($exam->id,$advise->recipient_id,$advise->recipient,'接受了['.$advise->author.']提出的建议。');
        // 添加热度记录
        $b_id = 52;
        ExamTemperatureRecord::recordAdd($exam->id,$advise->recipient_id,$b_id,$createtime);
        // 给反对作者发送通知
        User::find($advise->author_id)->notify(new ExamAdvisementAcceptedNotification($advise));
    }
}
