<?php

namespace App\Listeners\Classification;

use App\Events\Classification\ClassificationAddedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Announcement;
use App\Home\Classification;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class ClassificationAddedListener
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
     * @param  ClassificationAddedEvent  $event
     * @return void
     */
    public function handle(ClassificationAddedEvent $event)
    {
        //创建了新的分类后，公告到首页，暂时不考虑通知
        // 添加事件到用户动态
        $cls = $event->classification;
        // 更改缓存内容,put方法不设置过期时间不能写入
        // if(Cache::has('classification')){
        //     Cache::forget('classification',Classification::get());
        //     Cache::rememberForever('classification',Classification::get());
        // }else{
        //     Cache::add('classification',Classification::get());
        // }
        $behavior = '添加了'.$cls->level.'级分类名：';
        $objectName = $cls->classname;
        if($cls->level == 4){
            $objectURL = '/classification/underclass?id='.$cls->id.'&name='.$cls->classname;
        }else{
            $objectURL = '/classification/middleclass?id='.$cls->id.'&name='.$cls->classname;
        }
        $fromName = '内容分类';
        $fromURL = '/classification';
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($cls->creator_id,$cls->creator,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 发布公告，5代表分类,5代表创建
        
        Announcement::announcementAdd('5','5','内容'.$cls->level.'级分类名：<'.$cls->classname.'>已经创建。',$objectURL,$cls->created_at);
    }
}
