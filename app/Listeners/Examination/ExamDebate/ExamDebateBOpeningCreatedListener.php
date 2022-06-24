<?php

namespace App\Listeners\Examination\ExamDebate;

use App\Events\Examination\ExamDebate\ExamDebateBOpeningCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Examination\ExamDebate\ExamDebateBOpeningCreatedNotification;
use App\Notifications\Examination\ExamDebate\ExamDebateBOpeningCreatedToRefereeNotification;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamDebate\ExamDebateEvent;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamDebateBOpeningCreatedListener
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
     * @param  ExamDebateBOpeningCreatedEvent  $event
     * @return void
     */
    public function handle(ExamDebateBOpeningCreatedEvent $event)
    {
        //辩方开篇陈词后，需要写入辩论事件，并通知攻方辩方已经回应
        //添加事件到辩论事件表
        $debate = $event->examDebate;
        $exam = Exam::find($debate->exam_id);
        ExamDebateEvent::debateEventAdd($debate->id,$debate->Bauthor_id,$debate->Bauthor,'回应了'.$debate->Aauthor.'发起的攻辩:['.$debate->title.']；发表了辩方开篇陈词'); 
        //发表了有效的立论及陈词后，积分和成长值+100
        User::expAndGrowValue($debate->Bauthor_id,'100','100');
        // 写入用户动态
        $behavior = '写入辩方陈词，回应攻辩：';
        $objectName = $debate->title;
        $objectURL = '/examination/debate/'.$exam->id.'/'.$exam->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($debate->Bauthor_id,$debate->Bauthor,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加热度记录
        $b_id = 61;
        ExamTemperatureRecord::recordAdd($exam->id,$debate->Bauthor_id,$b_id,$createtime);
        // 发送通知给发布辩论用户
        User::find($debate->Aauthor_id)->notify(new ExamDebateBOpeningCreatedNotification($debate));
        if($debate->referee_id){
            User::find($debate->referee_id)->notify(new ExamDebateBOpeningCreatedToRefereeNotification($debate));
        }
    }
}
