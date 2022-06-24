<?php

namespace App\Listeners\Examination\Recommend;

use App\Events\Examination\Recommend\ExamTemperatureRecordAddedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Examination\Exam;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Examination\Recommend\ExamTemperature;
use App\Home\Personnel\Behavior;
use Illuminate\Support\Facades\Redis;

class ExamTemperatureRecordAddedListener
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
     * @param  ExamTemperatureRecordAddedEvent  $event
     * @return void
     */
    public function handle(ExamTemperatureRecordAddedEvent $event)
    {
        // 记录写入成功后，更新内容的热度
        $record = $event->examTemperatureRecord;
        $exam_id = $record->exam_id;
        $ex = Exam::find($exam_id);
        $cid = $ex->cid;
        $tem = ExamTemperature::where('exam_id',$exam_id)->first();
        // 这里更新返回的是更新后的模型
        $tem = $tem ?  $tem : ExamTemperature::recordInitialization($exam_id);
        $behavior = Behavior::find($record->behavior_id);
        // 变更热度热度
        Redis::INCRBY('exam:temperature:'.$exam_id,$behavior->score);
        Redis::ZINCRBY('exam:temperature:rank',$behavior->score,$exam_id); //百科总热度榜
        if($ex->level>=4) {
            Redis::ZINCRBY('exam:featured:temperature:rank',$behavior->score,$exam_id);
            Redis::ZINCRBY('exam:featured:classification:temperature:rank:'.$cid,$behavior->score,$exam_id);
            if($ex->level==5) {
                Redis::ZINCRBY('exam:pr:temperature:rank',$behavior->score,$exam_id);
                Redis::ZINCRBY('exam:pr:classification:temperature:rank:'.$cid,$behavior->score,$exam_id);
            }
        }
        Redis::ZINCRBY('classification:temperature:rank',$behavior->score,$cid);//分类总热度榜
        Redis::ZINCRBY('exam:classification:temperature:rank:'.$cid,$behavior->score,$exam_id);//内容带分类热度榜
        // $score = $tem->temperature + $behavior->score;
        // // 更新热度
        // ExamTemperature::recommendationUpdate($tem->id,$score);
    }
}
