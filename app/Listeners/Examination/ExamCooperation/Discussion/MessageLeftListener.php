<?php

namespace App\Listeners\Examination\ExamCooperation\Discussion;

use App\Events\Examination\ExamCooperation\Discussion\MessageLeftEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\ExamCooperation\ExamCooperationEvent;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use Carbon\Carbon;
use App\Models\User;

class MessageLeftListener
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
     * @param  MessageLeftEvent  $event
     * @return void
     */
    public function handle(MessageLeftEvent $event)
    {
        //用户在协作计划的留言，目前只需要写入协作事件中
        // 添加协作事件
        $msg = $event->examCooperationMessage;
        $exam_id = ExamCooperation::find($msg->cooperation_id)->exam_id;
        $createtime = Carbon::now();
        User::expAndGrowValue($msg->author_id,'10','10');
        ExamCooperationEvent::cooperationEventAdd($msg->cooperation_id,$msg->author_id,$msg->author,'在协作计划发表了讨论留言:'.$msg->title.'。');
        // 词条添加热度记录
        $b_id = 20;
        ExamTemperatureRecord::recordAdd($exam_id,$msg->author_id,$b_id,$createtime);
    }
}
