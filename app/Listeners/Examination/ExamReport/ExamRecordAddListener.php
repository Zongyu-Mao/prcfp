<?php

namespace App\Listeners\Examination\ExamReport;

use App\Events\Examination\ExamReport\ExamRecordAddEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Home\Examination\Exam;
use App\Home\Examination\Exam\ExamReport\ExamRecord;

class ExamRecordAddListener
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
     * @param  ExamRecordAddEvent  $event
     * @return void
     */
    public function handle(ExamRecordAddEvent $event)
    {
        //
        $record = $event->examRecord;
        $exam_id = $record->exam_id;
        $exam = Exam::find($exam_id);
        $res = ExamRecord::where('exam_id',$exam_id)->get();
        $avg_score = $this->inte_number($res->avg('score'));
        $avg_rate = $this->inte_number($res->avg('rate'));
        Exam::recordUpdate($exam_id,$avg_score,$avg_rate);
    }

    private function inte_number($number) {
        $n = intval($number);
        if($number - $n >= 0.5){
            $n +=$n;
        }
        return $n;
    }
}
