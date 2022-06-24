<?php

namespace App\Listeners\Encyclopedia\Entry\EntryExtendedReading;

use App\Events\Encyclopedia\Entry\EntryExtendedReading\EntryExtendedReadingDeletedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationEvent;
use App\Notifications\Encyclopedia\Entry\EntryExtendedReading\EntryExtendedReadingDeletedNotification;
use App\Notifications\Encyclopedia\Entry\EntryExtendedReading\EntryExtendedReadingBeenDeletedNotification;
use Illuminate\Support\Facades\Notification;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryDynamic;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EntryExtendedReadingDeletedListener
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
     * @param  EntryExtendedReadingDeletedEvent  $event
     * @return void
     */
    public function handle(EntryExtendedReadingDeletedEvent $event)
    {
        // 得到延伸词条、被延伸词条
        $ex = $event->entryExtendedEntryReading;
        $entry = Entry::find($ex->eid);
        $extended = Entry::find($ex->extended_id);
        //该处所要处理的是延伸阅读添加后，通知本词条相关用户该信息
        $cooperation = EntryCooperation::find($entry->cooperation_id);
        $createtime = Carbon::now();
        // 主动词条添加热度记录
        $b_id = 11;
        EntryTemperatureRecord::recordAdd($entry->id,auth('api')->user()->id,$b_id,$createtime);
        // 被延伸著作添加热度记录，注意后期补上
        $be_id = 13;
        EntryTemperatureRecord::recordAdd($extended->id,auth('api')->user()->id,$be_id,$createtime);
        // 添加协作事件
        EntryCooperationEvent::cooperationEventAdd($cooperation->id,auth('api')->user()->id,auth('api')->user()->username,'删除了延伸阅读《'.$extended->title.'》。');
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
        Notification::send($usersToNotification, new EntryExtendedReadingDeletedNotification($ex));

        // 添加事件到用户动态
        $behavior = '删除了延伸阅读：';
        $objectName = $extended->title;
        $objectURL = '/encyclopedia/reading/'.$extended->id.'/'.$extended->title;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd(auth('api')->user()->id,auth('api')->user()->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $entryBehavior = '已经删除延伸阅读';
        EntryDynamic::dynamicAdd($entry->id,$entry->title,$entryBehavior,$objectName,$objectURL,$createtime);
        // 添加事件到被延伸词条动态
        $extendBehavior = '已经被解除延伸阅读关系';
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
        Notification::send($usersToNotification, new EntryExtendedReadingBeenDeletedNotification($ex));
    }
}
