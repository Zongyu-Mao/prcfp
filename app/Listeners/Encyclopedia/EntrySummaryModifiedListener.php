<?php

namespace App\Listeners\Encyclopedia;

use App\Events\Encyclopedia\EntrySummaryModifiedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Home\Encyclopedia\EntryCooperation;
use App\Notifications\Encyclopedia\Entry\EntrySummaryModifiedNotification;
use App\Home\Encyclopedia\EntryDynamic;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EntrySummaryModifiedListener
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
     * @param  EntrySummaryModifiedEvent  $event
     * @return void
     */
    public function handle(EntrySummaryModifiedEvent $event)
    {
        // 添加事件到用户动态
        $behavior = '编辑了词条摘要：';
        $objectName = $event->entry->title;
        $objectURL = '/encyclopedia/reading/'.$event->entry->id.'/'.$event->entry->title;
        $fromName = '词条：'.$event->entry->title;
        $fromURL = '/encyclopedia/cooperation/'.$event->entry->id.'/'.$event->entry->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd(auth('api')->user()->id,auth('api')->user()->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $entryBehavior = '正文摘要已经修改';
        EntryDynamic::dynamicAdd($event->entry->id,$event->entry->title,$entryBehavior,$objectName,$objectURL,$createtime);
        // 通知协作组成员词条摘要已经修改
        $cooperation = EntryCooperation::where([['eid',$event->entry->id],['status','0']])->first();
        $manage_id = $event->entry->manage_id;
        if($cooperation){
            $crewArr = $cooperation->crews()->pluck('user_id')->toArray();
            $initiate_id = $cooperation->manage_id;
            array_push($crewArr, $manage_id);
            array_push($crewArr, $initiate_id); 
        }else{
            $crewArr = [];
            array_push($crewArr, $manage_id);
        }
        array_unique($crewArr);
        $usersToNotification = User::whereIn('id',$crewArr)->get();
        Notification::send($usersToNotification, new EntrySummaryModifiedNotification($event->entry));
    }
}
