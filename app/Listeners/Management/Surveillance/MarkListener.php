<?php

namespace App\Listeners\Management\Surveillance;

use App\Events\Management\Surveillance\MarkEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Management\Surveillance\EntryMarked;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\EntryDynamic;
use Carbon\Carbon;
use App\Models\User;

class MarkListener
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
     * @param  MarkEvent  $event
     * @return void
     */
    public function handle(MarkEvent $event)
    {
        // 标记发生后
        $mark = $event->surveillanceMark;
        // 获取协作组
        $entry = $mark->content;
        $objectName = $entry->title;
        $behavior = '主内容已被标记';
        $objectURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        $createtime = Carbon::now();
        // 写入动态
        EntryDynamic::dynamicAdd($entry->id,$entry->title,$behavior,$objectName,$objectURL,$createtime);
        $cooperation = EntryCooperation::find($entry->cooperation_id);
        $crewArr = [];
        if($cooperation){
            $crewArr = $cooperation->crews()->pluck('user_id')->toArray();
        } 
        array_push($crewArr, $entry->manage_id);
        // mark 要通知协作组
        $usersToNotification = User::whereIn('id',$crewArr)->get();
        Notification::send($usersToNotification, new EntryMarked($mark));
    }
}
