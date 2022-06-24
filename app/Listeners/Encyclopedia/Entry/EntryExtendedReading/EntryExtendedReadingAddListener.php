<?php

namespace App\Listeners\Encyclopedia\Entry\EntryExtendedReading;

use App\Events\Encyclopedia\Entry\EntryExtendedReading\EntryExtendedReadingAddEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationEvent;
use App\Notifications\Encyclopedia\Entry\EntryExtendedReading\EntryExtendedReadingAddNotification;
use App\Notifications\Encyclopedia\Entry\EntryExtendedReading\EntryExtendedReadingBeenAddedNotification;
use Illuminate\Support\Facades\Notification;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryDynamic;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryExtendedReadingAddListener
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
     * @param  EntryExtendedReadingAddEvent  $event
     * @return void
     */
    public function handle(EntryExtendedReadingAddEvent $event)
    {
        
        // 得到延伸词条、被延伸词条
        $ex = $event->entryExtendedEntryReading;
        $entry = Entry::find($ex->eid);
        $extended = Entry::find($ex->extended_id);
        //该处所要处理的是延伸阅读添加后，通知本词条相关用户该信息
        $cooperation = EntryCooperation::find($entry->cooperation_id);
        $user = User::find($ex->creator_id);
        $createtime = Carbon::now();
        // 主动词条添加热度记录
        $b_id = 10;
        EntryTemperatureRecord::recordAdd($entry->id,$user->id,$b_id,$createtime);
        // 被延伸著作添加热度记录，注意后期补上*********************************************************************************
        $be_id = 12;
        EntryTemperatureRecord::recordAdd($extended->id,$user->id,$be_id,$createtime);
        // 添加协作事件
        EntryCooperationEvent::cooperationEventAdd($cooperation->id,$user->id,$user->username,'添加了延伸阅读《'.$extended->title.'》。');
        // 开启对延伸词条协作组成员和关注词条用户的通知
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
        $focusUsers = $entry->entryFocus()->pluck('user_id')->toArray();
        // 合并协作组与兴趣用户
        $users = array_unique(array_merge($crewArr,$focusUsers));
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyEntryAdd($result));
        Notification::send($usersToNotification, new EntryExtendedReadingAddNotification($ex));

        // 添加事件到用户动态
        $behavior = '添加了延伸阅读：';
        $objectName = $extended->title;
        $objectURL = '/encyclopedia/reading/'.$extended->id.'/'.$extended->title;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($user->id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $entryBehavior = '已经添加延伸阅读';
        EntryDynamic::dynamicAdd($entry->id,$entry->title,$entryBehavior,$objectName,$objectURL,$createtime);
        // 添加事件到被延伸词条动态
        $extendBehavior = '已经被添加为延伸阅读';
        EntryDynamic::dynamicAdd($extended->id,$extended->title,$extendBehavior,$fromName,$fromURL,$createtime);

        // 该处所要处理的是延伸阅读添加后，通知本词条相关用户该信息
        $cooperationExtended = EntryCooperation::find($extended->cooperation_id); 
        // 开启对被延伸词条协作组成员和关注词条用户的通知
        $manage = $extended->manage_id;
        if($cooperationExtended){
            $extendedCrewArr = $cooperationExtended->crews()->pluck('user_id')->toArray();
            $initiate = $cooperationExtended->manage_id;
            array_push($extendedCrewArr, $manage);
            array_push($extendedCrewArr, $initiate); 
        }else{
            $extendedCrewArr = [];
            array_push($extendedCrewArr, $manage);
        }
        // 获取被延伸词条的关注用户
        $focus = $extended->entryFocus()->pluck('user_id')->toArray();
        // 合并协作组与兴趣用户
        $notificationUsers = array_unique(array_merge($extendedCrewArr,$focus));
        $userToNotification = User::whereIn('id',$notificationUsers)->get();
        Notification::send($usersToNotification, new EntryExtendedReadingBeenAddedNotification($ex));     
    }
}
