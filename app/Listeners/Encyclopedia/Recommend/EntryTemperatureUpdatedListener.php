<?php

namespace App\Listeners\Encyclopedia\Recommend;

use App\Events\Encyclopedia\Recommend\EntryTemperatureUpdatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\Recommend\EntryRecommendation;

class EntryTemperatureUpdatedListener
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
     * @param  EntryTemperatureUpdatedEvent  $event
     * @return void
     */
    public function handle(EntryTemperatureUpdatedEvent $event)
    {
        //这里主要是确认推荐表是否需要更改，即对比本词条是否在推荐表中，如果不在其热度是否大于推荐表中该分类的词条热度
        $tem = $event->entryTemperature;
        $cid = Entry::find($tem->eid)->cid;
        // 如果本分类没有记录，就直接把本条记录写入，当作初始化本分类推荐
        $recommend = EntryRecommendation::where('cid',$cid)->count() ? EntryRecommendation::where('cid',$cid)->first():EntryRecommendation::recommendationAdd($cid,$tem->eid);
        if($tem->eid != $recommend->eid){
            // 推荐表与对比词条不同，因此比较
            // 本词条的热度
            $temperature = $tem->temperature;
            // 原推荐热度
            $old_tem = $recommend->temperature;
            if($temperature > $old_tem){
                EntryRecommendation::recommendationUpdate($recommend->id,$tem->eid);
            }
        }
    }
}
