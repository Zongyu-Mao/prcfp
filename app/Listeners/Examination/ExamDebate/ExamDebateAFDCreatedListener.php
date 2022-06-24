<?php

namespace App\Listeners\Examination\ExamDebate;

use App\Events\Examination\ExamDebate\ExamDebateAFDCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Examination\ExamDebate\ExamDebateAFDCreatedNotification;
use App\Notifications\Examination\ExamDebate\ExamDebateAFDCreatedToRefereeNotification;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamDebate\ExamDebateEvent;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamDebateAFDCreatedListener
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
     * @param  ExamDebateAFDCreatedEvent  $event
     * @return void
     */
    public function handle(ExamDebateAFDCreatedEvent $event)
    {
        //攻方写入自由辩论内容后，需要写入词条辩论事件，并通知辩方攻方已经回应
        //添加事件到辩论事件表
        $debate = $event->examDebate;
        $exam = Exam::find($debate->exam_id);
        ExamDebateEvent::debateEventAdd($debate->id,$debate->Aauthor_id,$debate->Aauthor,'在其发起的攻辩:['.$debate->title.']发表了攻方自由辩论。'); 
        //发表了有效的立论及陈词后，积分和成长值+100
        User::expAndGrowValue($debate->Aauthor_id,'100','100');
        // 写入用户动态
        $behavior = '写入攻方自由辩论，在发起的攻辩：';
        $objectName = $debate->title;
        $objectURL = '/examination/debate/'.$exam->id.'/'.$exam->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($debate->Aauthor_id,$debate->Aauthor,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加热度记录
        $b_id = 60;
        ExamTemperatureRecord::recordAdd($exam->id,$debate->Aauthor_id,$b_id,$createtime);
        // 发送通知给辩方用户
        User::find($debate->Bauthor_id)->notify(new ExamDebateAFDCreatedNotification($debate));
        if($debate->referee_id){
            User::find($debate->referee_id)->notify(new ExamDebateAFDCreatedToRefereeNotification($debate));
        }
    }
}
