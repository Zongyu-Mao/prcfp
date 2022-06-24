<?php

namespace App\Listeners\Management\Surveillance;

use App\Events\Management\Surveillance\SurveillanceArticleEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use App\Home\Publication\Article;
use App\Home\Publication\Article\ArticleDynamic;
use Carbon\Carbon;

class SurveillanceArticleListener
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
     * @param  SurveillanceArticleEvent  $event
     * @return void
     */
    public function handle(SurveillanceArticleEvent $event)
    {
        // 巡查如果通过，要变更主内容的surveillance
        $s = $event->surveillanceArticleRecord;
        $c = Article::find($s->sid);
        $objectName = $c->title;
        $behavior = $s->stand==2?'主内容巡查未通过':'主内容通过巡查';
        $objectURL = '/publication/reading/'.$c->id.'/'.$c->title;
        $createtime = Carbon::now();
        if($s->status==0) {
            // 创建且不通过，此时不需要操作主内容
            
        }else if($s->status==1) {
            // 写入的是committee了，另算
            $behavior = '请求通过巡查';
        }else if($s->status==2) {
            // 已经通过，就看stand了
        }
        // 写入动态
        ArticleDynamic::dynamicAdd($c->id,$c->title,$behavior,$objectName,$objectURL,$createtime);
        
    }
}
