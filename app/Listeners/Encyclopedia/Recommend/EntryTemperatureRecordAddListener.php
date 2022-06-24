<?php

namespace App\Listeners\Encyclopedia\Recommend;

use App\Events\Encyclopedia\Recommend\EntryTemperatureRecordAddEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Home\Encyclopedia\Entry;
use App\Home\Personnel\Behavior;
use Illuminate\Support\Facades\Redis;

class EntryTemperatureRecordAddListener
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
     * @param  EntryTemperatureRecordAddEvent  $event
     * @return void
     */
    public function handle(EntryTemperatureRecordAddEvent $event)
    {
        // 记录写入成功后，更新内容的热度
        $record = $event->entryTemperatureRecord;
        $eid = $record->eid;
        $e = Entry::find($eid);
        $cid = $e->cid;
        // 这里更新返回的是更新后的模型
        $behavior = Behavior::find($record->behavior_id);
        // 变更热度热度
        Redis::INCRBY('entry:temperature:'.$eid,$behavior->score);
        Redis::ZINCRBY('entry:temperature:rank',$behavior->score,$eid); //百科总热度榜
        // 添加等级为4/5的优良条目榜单；添加带分类
        if($e->level>=4) {
            Redis::ZINCRBY('entry:featured:temperature:rank',$behavior->score,$eid);
            Redis::ZINCRBY('entry:featured:classification:temperature:rank:'.$cid,$behavior->score,$eid);
            if($e->level==5) {
                Redis::ZINCRBY('entry:pr:temperature:rank',$behavior->score,$eid);
                Redis::ZINCRBY('entry:pr:classification:temperature:rank:'.$cid,$behavior->score,$eid);
            }
        }
        // 添加等级为5的极参条目榜单；添加带分类
        Redis::ZINCRBY('classification:temperature:rank',$behavior->score,$cid);//分类总热度榜
        Redis::ZINCRBY('entry:classification:temperature:rank:'.$cid,$behavior->score,$eid);//内容带分类热度榜
    }
}
