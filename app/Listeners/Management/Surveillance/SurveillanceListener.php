<?php

namespace App\Listeners\Management\Surveillance;

use App\Events\Management\Surveillance\SurveillanceEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryDynamic;
use Carbon\Carbon;

class SurveillanceListener
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
     * @param  SurveillanceEvent  $event
     * @return void
     */
    public function handle(SurveillanceEvent $event)
    {
        //
        $s = $event->surveillanceRecord;
        $c = Entry::find($s->sid);
        $objectName = $c->title;
        $behavior = $s->stand==2?'主内容巡查未通过':'主内容通过巡查';
        $objectURL = '/encyclopedia/reading/'.$c->id.'/'.$c->title;
        $createtime = Carbon::now();
        if($s->status==0) {
            // 创建且不通过，此时不需要操作主内容
            
        }else if($s->status==1) {
            // 写入的是committee了，另算
            $behavior = '请求通过巡查';
        }else if($s->status==2) {
            // 已经通过，就看stand了
        }
        // 写入词条的动态
        EntryDynamic::dynamicAdd($c->id,$c->title,$behavior,$objectName,$objectURL,$createtime);
    }
}
