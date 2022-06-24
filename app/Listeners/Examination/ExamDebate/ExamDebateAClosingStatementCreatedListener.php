<?php

namespace App\Listeners\Examination\ExamDebate;

use App\Events\Examination\ExamDebate\ExamDebateAClosingStatementCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Examination\ExamDebate\ExamDebateAClosingStatementCreatedNotification;
use App\Notifications\Examination\ExamDebate\ExamDebateACSCreatedNotification;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamDebate\ExamDebateEvent;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamDebateAClosingStatementCreatedListener
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
     * @param  ExamDebateAClosingStatementCreatedEvent  $event
     * @return void
     */
    public function handle(ExamDebateAClosingStatementCreatedEvent $event)
    {
        //攻方写入总结陈词内容后，需要写入词条辩论事件，并通知辩方攻方已经回应，注意如果有裁判，应通知裁判
        //添加事件到辩论事件表
        $debate = $event->examDebate;
        $exam = Exam::find($debate->exam_id);
        ExamDebateEvent::debateEventAdd($debate->id,$debate->Aauthor_id,$debate->Aauthor,'在发起的攻辩:['.$debate->title.']发表了攻方总结陈词。'); 
        //发表了有效的总结陈词后，积分和成长值+100
        User::expAndGrowValue($debate->Aauthor_id,'100','100');
        // 写入用户动态
        $behavior = '写入攻方总结陈词，在发起的攻辩：';
        $objectName = $debate->title;
        $objectURL = '/examination/debate/'.$exam->id.'/'.$exam->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($debate->Aauthor_id,$debate->Aauthor,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加热度记录
        $b_id = 60;
        ExamTemperatureRecord::recordAdd($exam->id,$debate->Aauthor_id,$b_id,$createtime);
        // 发送通知给攻方用户
        User::find($debate->Bauthor_id)->notify(new ExamDebateAClosingStatementCreatedNotification($debate));
        if($debate->referee_id){
            User::find($debate->referee_id)->notify(new ExamDebateACSCreatedToRefereeNotification($debate));
        }
    }
}
