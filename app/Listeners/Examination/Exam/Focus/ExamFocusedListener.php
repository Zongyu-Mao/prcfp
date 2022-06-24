<?php

namespace App\Listeners\Examination\Exam\Focus;

use App\Events\Examination\Exam\Focus\ExamFocusedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ExamFocusedListener
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
     * @param  ExamFocusedEvent  $event
     * @return void
     */
    public function handle(ExamFocusedEvent $event)
    {
        //关注后的热度更新
        $focus = $event->examFocusUser;
        // 添加热度记录
        $b_id = 4;

        ExamTemperatureRecord::recordAdd($focus->exam_id,auth('api')->user()->id,$b_id,Carbon::now());
    }
}
