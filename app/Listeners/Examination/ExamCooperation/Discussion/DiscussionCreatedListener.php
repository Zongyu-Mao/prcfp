<?php

namespace App\Listeners\Examination\ExamCooperation\Discussion;

use App\Events\Examination\ExamCooperation\Discussion\DiscussionCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\ExamCooperation\ExamCooperationEvent;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Models\User;

class DiscussionCreatedListener
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
     * @param  DiscussionCreatedEvent  $event
     * @return void
     */
    public function handle(DiscussionCreatedEvent $event)
    {
        //协作计划讨论是内部事宜，仅需写入协作事件
        // 添加协作事件
        $discussion = $event->examCooperationDiscussion;
        $exam = Exam::find(ExamCooperation::find($discussion->cooperation_id)->exam_id);
        ExamCooperationEvent::cooperationEventAdd($discussion->cooperation_id,$discussion->author_id,$discussion->author,'发表了了协作计划讨论。');
        User::expAndGrowValue($discussion->author_id,5,5);
        // 词条添加热度记录
        $b_id = 19;
        ExamTemperatureRecord::recordAdd($exam->id,$discussion->author_id,$b_id,$discussion->created_at);
    }
}
