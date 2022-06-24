<?php

namespace App\Listeners\Examination\ExamCooperation;

use App\Events\Examination\ExamCooperation\ExamCooperationAssignModifiedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Examination\ExamCooperation\ExamCooperationEvent;
use Illuminate\Support\Facades\Auth;

class ExamCooperationAssignModifiedListener
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
     * @param  ExamCooperationAssignModifiedEvent  $event
     * @return void
     */
    public function handle(ExamCooperationAssignModifiedEvent $event)
    {
        //assign变化仅需要记录在事件中
        // 添加协作事件
        $user = auth('api')->user();
        ExamCooperationEvent::cooperationEventAdd($event->examCooperation->id,$user->id,$user->username,'编辑了新的协作任务。');
    }
}
