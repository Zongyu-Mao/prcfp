<?php

namespace App\Listeners\Encyclopedia\EntryCooperation;

use App\Events\Encyclopedia\EntryCooperation\EntryCooperationMessageLeftEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Encyclopedia\EntryCooperation\EntryCooperationMessageLeftNotification;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationEvent;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryCooperationMessageLeftListener
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
     * @param  EntryCooperationMessageLeftEvent  $event
     * @return void
     */
    public function handle(EntryCooperationMessageLeftEvent $event)
    {
        //有用户在协作计划页面留言，仅需通知协作计划成员
        $message = $event->entryCooperationMessage;
        $cooperation = EntryCooperation::find($message->cooperation_id);
        $entry = Entry::find($cooperation->eid);
        $manage_id = $entry->manage_id;
        User::expAndGrowValue($message->author_id,'10','10');
        $createtime = Carbon::now();
        // 添加协作事件
        EntryCooperationEvent::cooperationEventAdd($message->cooperation_id,$message->author_id,$message->author,'发表留言：['.$message->title.']。');
        // 词条添加热度记录
        $b_id = 20;
        EntryTemperatureRecord::recordAdd($entry->id,$message->author_id,$b_id,$createtime);
        // 添加事件到用户动态
        $behavior = '发表了对协作小组留言：';
        $objectName = $message->title;
        $objectURL = '/encyclopedia/cooperation/'.$entry->id.'/'.$entry->title.'#cooperationMessage'.$message->id;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        
        UserDynamic::dynamicAdd($message->author_id,$message->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 开启对协作组成员的通知
        $manage_id = $entry->manage_id;
        if($cooperation){
            $crewArr = $cooperation->crews()->pluck('user_id')->toArray();
            $initiate_id = $cooperation->manage_id;
            array_push($crewArr, $manage_id);
            array_push($crewArr, $initiate_id); 
        }else{
            $crewArr = [];
            array_push($crewArr, $manage_id);
        }
        // 获取词条的关注用户
        // $focusUsers = $entry->entryFocus()->pluck('user_id')->toArray();
        // 合并协作组与兴趣用户
        // $users = array_unique(array_merge($crewArr,$focusUsers));
        $users = array_unique($crewArr);
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyEntryAdd($result));
        Notification::send($usersToNotification, new EntryCooperationMessageLeftNotification($message));
    }
}
