<?php

namespace App\Listeners\Examination\ExamCooperation\Discussion;

use App\Events\Examination\ExamCooperation\Discussion\MessageRepliedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Examination\ExamCooperation\ExamCooperationMessage\ExamCooperationMessageRepliedNotification;
use App\Home\Examination\ExamCooperation\ExamCooperationMessage;
use App\Home\Examination\ExamCooperation\ExamCooperationEvent;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use Carbon\Carbon;
use App\Models\User;

class MessageRepliedListener
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
     * @param  MessageRepliedEvent  $event
     * @return void
     */
    public function handle(MessageRepliedEvent $event)
    {
        //回复还要找到原作者并通知，写入协作事件
        // 找到回复
        $cooperationMsg = $event->examCooperationMessage;
        $message = ExamCooperationMessage::find($cooperationMsg->pid);
        $exam_id = ExamCooperation::find($cooperationMsg->cooperation_id)->exam_id;
        
        // 添加协作事件
        ExamCooperationEvent::cooperationEventAdd($cooperationMsg->cooperation_id,$cooperationMsg->author_id,$cooperationMsg->author,'回复了'.$message->author.'在协作计划发表的讨论留言。');
        User::expAndGrowValue($message->author_id,'10','10');
        // 添加热度记录
        $b_id = 21;
        ExamTemperatureRecord::recordAdd($exam_id,$message->author_id,$b_id,Carbon::now());
        // 通知用户留言被回复
        User::find($message->author_id)->notify(new ExamCooperationMessageRepliedNotification($cooperationMsg));
    }
}
