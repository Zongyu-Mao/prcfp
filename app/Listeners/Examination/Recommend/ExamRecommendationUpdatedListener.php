<?php

namespace App\Listeners\Examination\Recommend;

use App\Events\Examination\Recommend\ExamRecommendationUpdatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Examination\Recommend\ExamRecommendRecord;

class ExamRecommendationUpdatedListener
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
     * @param  ExamRecommendationUpdatedEvent  $event
     * @return void
     */
    public function handle(ExamRecommendationUpdatedEvent $event)
    {
        //更新推荐记录
        $rec = $event->examRecommendation;
        $createtime = $rec->updated_at;
        ExamRecommendRecord::recordAdd($rec->cid,$rec->exam_id,$createtime);
    }
}
