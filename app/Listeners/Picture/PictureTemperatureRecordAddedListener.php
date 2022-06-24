<?php

namespace App\Listeners\Picture;

use App\Events\Picture\PictureTemperatureRecordAddedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Carbon\Carbon;
use App\Models\Picture\Picture;
use App\Models\Picture\PictureTemperatureRecord;
use App\Models\Picture\PictureTemperature;
use App\Home\Personnel\Behavior;
use Illuminate\Support\Facades\Redis;

class PictureTemperatureRecordAddedListener
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
     * @param  PictureTemperatureRecordAddedEvent  $event
     * @return void
     */
    public function handle(PictureTemperatureRecordAddedEvent $event)
    {
        // 记录写入成功后，更新内容的热度
        $record = $event->pictureTemperatureRecord;
        $picture_id = $record->picture_id;
        $time = Carbon::now()->toDateString();
        $cid = Picture::find($picture_id)->cid;
        $tem = PictureTemperature::where('picture_id',$picture_id)->first();
        // 这里更新返回的是更新后的模型
        $tem = $tem ?  $tem : PictureTemperature::recordInitialization($picture_id);
        $behavior = Behavior::find($record->behavior_id);
        // 变更热度热度
        Redis::INCRBY('picture:temperature:'.$picture_id,$behavior->score);
        Redis::ZINCRBY('picture:temperature:rank',$behavior->score,$picture_id); //总热度榜
        Redis::ZINCRBY('classification:temperature:rank',$behavior->score,$cid);//分类总热度榜
        Redis::ZINCRBY('picture:classification:temperature:rank:'.$cid,$behavior->score,$picture_id);//内容带分类热度榜
        Redis::ZINCRBY('picture:classification:time:temperature:rank:'.$cid.':'.$time,$behavior->score,$picture_id);//带分类、带时间热度
        // Redis::ZINCRBY('picture:time:temperature:rank:'.$time,$behavior->score,$picture_id);//带时间热度 带时间暂时不做
    }
}
