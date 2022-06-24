<?php

namespace App\Listeners\Examination\ExamDebate\ExamDebateReferee;

use App\Events\Examination\ExamDebate\ExamDebateReferee\ExamDebateAnalyseUpdatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamDebate\ExamDebateEvent;
use App\Notifications\Examination\ExamDebate\ExamDebateReferee\ExamDebateAnalyseUpdatedNotification;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamDebateAnalyseUpdatedListener
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
     * @param  ExamDebateAnalyseUpdatedEvent  $event
     * @return void
     */
    public function handle(ExamDebateAnalyseUpdatedEvent $event)
    {
        //裁判分析的更新，只需要写入事件，写入用户动态，通知攻辩双方
        //添加事件到辩论事件表
        $debate = $event->examDebate;
        $exam = Exam::find($debate->exam_id);
        ExamDebateEvent::debateEventAdd($debate->id,$debate->referee_id,$debate->referee,'更新了裁判分析。'); 
        // 添加事件到用户动态
        $behavior = '发表/更新了裁判分析，在攻辩：';
        $objectName = $debate->title;
        $objectURL = '/examination/debate/'.$exam->id.'/'.$exam->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($debate->referee_id,$debate->referee,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加热度记录
        $b_id = 63;
        if(ExamTemperatureRecord::where([['exam_id',$exam->id],['user_id',$debate->referee_id],['behavior_id',$b_id]])->count() < 3){
             ExamTemperatureRecord::recordAdd($exam->id,$debate->referee_id,$b_id,$createtime);
        }
        // 发送通知给辩论攻方
        User::find($debate->Aauthor_id)->notify(new ExamDebateAnalyseUpdatedNotification($debate));
        // 发送通知给辩论辩方
        User::find($debate->Bauthor_id)->notify(new ExamDebateAnalyseUpdatedNotification($debate));
    }
}
