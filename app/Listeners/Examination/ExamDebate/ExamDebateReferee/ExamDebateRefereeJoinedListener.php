<?php

namespace App\Listeners\Examination\ExamDebate\ExamDebateReferee;

use App\Events\Examination\ExamDebate\ExamDebateReferee\ExamDebateRefereeJoinedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamDebate\ExamDebateEvent;
use App\Notifications\Examination\ExamDebate\ExamDebateReferee\ExamDebateRefereeJoinedNotification;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamDebateRefereeJoinedListener
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
     * @param  ExamDebateRefereeJoinedEvent  $event
     * @return void
     */
    public function handle(ExamDebateRefereeJoinedEvent $event)
    {
        //裁判加入仅需写入辩论事件，用户动态并通知攻辩双方
        //添加事件到辩论事件表
        $debate = $event->examDebate;
        $exam = Exam::find($debate->exam_id);
        ExamDebateEvent::debateEventAdd($debate->id,$debate->referee_id,$debate->referee,'成为裁判'); 
        // 添加事件到用户动态
        $behavior = '成为裁判，在攻辩：';
        $objectName = $debate->title;
        $objectURL = '/examination/debate/'.$exam->id.'/'.$exam->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($debate->referee_id,$debate->referee,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加热度记录
        $b_id = 62;
        ExamTemperatureRecord::recordAdd($exam->id,$debate->referee_id,$b_id,$createtime);
        // 发送通知给辩论攻方
        User::find($debate->Aauthor_id)->notify(new ExamDebateRefereeJoinedNotification($debate));
        // 发送通知给辩论辩方
        User::find($debate->Bauthor_id)->notify(new ExamDebateRefereeJoinedNotification($debate));
    }
}
