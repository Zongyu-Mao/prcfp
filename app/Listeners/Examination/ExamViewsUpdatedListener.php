<?php

namespace App\Listeners\Examination;

use App\Events\Examination\ExamViewsUpdatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Personnel\Behavior;
use App\Home\Examination\Recommend\ExamTemperature;

class ExamViewsUpdatedListener
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
     * @param  ExamViewsUpdatedEvent  $event
     * @return void
     */
    public function handle(ExamViewsUpdatedEvent $event)
    {
        //浏览量的增加会引起热度的更新，但是不更新到热度记录
        $exam = $event->exam;
        // 添加热度记录
        $t = Behavior::find(3)->score; 
        $ext = ExamTemperature::where('exam_id',$exam->id)->first();
        $old_tem = $ext->temperature;
        $tem = $old_tem + $t;
        ExamTemperature::recommendationUpdate($ext->id,$tem);
    }
}
