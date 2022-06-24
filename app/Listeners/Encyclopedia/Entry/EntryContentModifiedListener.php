<?php

namespace App\Listeners\Encyclopedia\Entry;

use App\Events\Encyclopedia\Entry\EntryContentModifiedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Encyclopedia\Entry\EntryContent;
use App\Home\Encyclopedia\Entry;
use Illuminate\Support\Facades\Notification;
use App\Home\Encyclopedia\EntryCooperation;
use App\Notifications\Encyclopedia\Entry\EntryContentModifiedNotification;
use App\Home\Encyclopedia\EntryDynamic;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;
use DB;

class EntryContentModifiedListener
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
     * @param  EntryContentModifiedEvent  $event
     * @return void
     */
    public function handle(EntryContentModifiedEvent $event)
    {
        //通知协作组成员和关注该词条的用户词条内容已经编辑
        // 获取协作组成员
        $content = $event->entryContent;
        $cooperation = EntryCooperation::where([['eid',$content->eid],['status','0']])->first();
        $entry = Entry::find($content->eid);
        $entry->increment('edit_number');
        $editorUser = User::find($content->editor_id);
        // 添加事件到用户动态
        $behavior = '编辑了著作正文第'.$content->sort.'部分：';
        $objectName = $entry->title;
        $objectURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($editorUser->id,$editorUser->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $entryBehavior = '正文第'.$content->sort.'部分已经重新编辑：';
        EntryDynamic::dynamicAdd($entry->id,$entry->title,$entryBehavior,$objectName,$objectURL,$createtime);
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
        Notification::send($usersToNotification, new EntryContentModifiedNotification($content));
    }
}
