<?php

namespace App\Listeners\Encyclopedia\Entry\EntryReference;

use App\Events\Encyclopedia\Entry\EntryReference\EntryReferenceModifiedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Encyclopedia\Entry;
use Illuminate\Support\Facades\Notification;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationEvent;
use App\Notifications\Encyclopedia\Entry\EntryReference\EntryReferenceModifiedNotification;
use App\Home\Encyclopedia\EntryDynamic;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EntryReferenceModifiedListener
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
     * @param  EntryReferenceModifiedEvent  $event
     * @return void
     */
    public function handle(EntryReferenceModifiedEvent $event)
    {
        //词条参考文献修改后，添加该事件到协作动态，通知协作组成员和词条关注者参考文献新增了
        $ref = $event->entryReference;
        $entry = Entry::find($ref->entry_id);
        $entry->increment('edit_number');
        $cooperation = EntryCooperation::find($entry->cooperation_id);
        $user = User::find($ref->creator);
        // 添加事件到用户动态
        $behavior = '修改了参考文献：';
        $objectName = $ref->title;
        $objectURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title.'#entryReference'.$ref->id;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($user->id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $entryBehavior = '已经修改参考文献';
        EntryDynamic::dynamicAdd($entry->id,$entry->title,$entryBehavior,$objectName,$objectURL,$createtime);
        // 添加协作事件
        EntryCooperationEvent::cooperationEventAdd($cooperation->id,$user->id,$user->username,'修改了参考文献['.$ref->sort.']<'.$ref->title.'>。');
        // 开启对协作组成员和关注词条用户的通知
        $manage_id = $entry->manage_id;
        if(count($cooperation)){
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
        Notification::send($usersToNotification, new EntryReferenceModifiedNotification($ref));
    }
}
