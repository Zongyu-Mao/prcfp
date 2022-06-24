<?php

namespace App\Listeners\Examination\Recommend;

use App\Events\Examination\Recommend\ExamTemperatureUpdatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Examination\Exam;
use App\Home\Examination\Recommend\ExamRecommendation;

class ExamTemperatureUpdatedListener
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
     * @param  ExamTemperatureUpdatedEvent  $event
     * @return void
     */
    public function handle(ExamTemperatureUpdatedEvent $event)
    {
        //这里主要是确认推荐表是否需要更改，即对比本词条是否asdfasdf在推荐表中，如果不在其热度是否大于推荐表中该分类的词条热度
        $tem = $event->examTemperature;
        $cid = Exam::find($tem->exam_id)->cid;
        // 如果本分类没有记录，就直接把本条记录写入，当作初始化本分类推荐
        $recommend = ExamRecommendation::where('cid',$cid)->count() ? ExamRecommendation::where('cid',$cid)->first():ExamRecommendation::recommendationAdd($cid,$tem->exam_id);
        if($tem->exam_id != $recommend->exam_id){
            // 推荐表与对比词条不同，因此比较
            // 本词条的热度
            $temperature = $tem->temperature;
            // 原推荐热度
            $old_tem = $recommend->temperature;
            if($temperature > $old_tem){
                ExamRecommendation::recommendationUpdate($recommend->id,$tem->exam_id);
            }
        }
    }
}
