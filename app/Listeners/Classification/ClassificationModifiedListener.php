<?php

namespace App\Listeners\Classification;

use App\Events\Classification\ClassificationModifiedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Announcement;
use App\Home\Classification;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class ClassificationModifiedListener
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
     * @param  ClassificationModifiedEvent  $event
     * @return void
     */
    public function handle(ClassificationModifiedEvent $event)
    {
        //修改了分类后，公告到首页，暂时不考虑通知
        $cls = $event->classification;
        // 更改缓存内容
        // 添加事件到用户动态
        $behavior = '修改了'.$cls->level.'级分类名称：';
        $objectName = $cls->classname;
        if($cls->level == 4){
            $objectURL = '/classification/underclass?id='.$cls->id.'&name='.$cls->classname;
        }else{
            $objectURL = '/classification/middleclass?id='.$cls->id.'&name='.$cls->classname;
        }
        $fromName = '内容分类';
        $fromURL = '/classification';
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($cls->revisor_id,$cls->revisor,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 发布公告，5代表分类,0代表编辑/修改
        Announcement::announcementAdd('5','0','内容'.$cls->level.'级分类名：<'.$cls->classname.'>已经修改。',$objectURL,$cls->created_at);
    }
}
