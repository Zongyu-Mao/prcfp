<?php

namespace App\Listeners\Examination\ExamDebate\ExamDebateGiveLike;

use App\Events\Examination\ExamDebate\ExamDebateGiveLike\ExamDebateGivenLikeEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Examination\ExamDebate;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamDebate\ExamDebateEvent;
use App\Notifications\Examination\ExamDebate\ExamDebateGiveLike\ExamDebateGivenLikeNotification;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamDebateGivenLikeListener
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
     * @param  ExamDebateGivenLikeEvent  $event
     * @return void
     */
    public function handle(ExamDebateGivenLikeEvent $event)
    {
        //用户点赞后，记录事件，发送通知给被点赞方
        $record = $event->examDebateStarRecord;
        $debate = ExamDebate::find($record->debate_id);
        $exam = Exam::find($debate->exam_id);
        // 判断立场并更新辩论表的点赞数
        if($record->star == '0'){
            $standpoint = '送了一颗红星星给';
        }elseif($record->star == '1'){
            $standpoint = '送了一颗黑星星给';
        }
        // 判断对象
        if($record->object == '0'){
            $starObject = '攻方。';
            $notify_id = $debate->Aauthor_id;
        }elseif($record->object == '1'){
            $starObject = '辩方。';
            $notify_id = $debate->Bauthor_id;
        }elseif($record->object == '2'){
            $starObject = '裁判。';
            $notify_id = $debate->referee_id;
        }
        //添加事件到辩论事件表
        ExamDebateEvent::debateEventAdd($record->debate_id,$record->user_id,$record->username,$standpoint.$starObject); 
        // 添加事件到用户动态
        $behavior = $standpoint.$starObject.'在攻辩：';
        $objectName = $debate->title;
        $objectURL = '/examination/debate/'.$exam->id.'/'.$exam->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($record->user_id,$record->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 发送通知给被点赞方
        User::find($notify_id)->notify(new ExamDebateGivenLikeNotification($record));
    }
}
