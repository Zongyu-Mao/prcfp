<?php

namespace App\Listeners\Encyclopedia\EntryDiscussion;

use App\Events\Encyclopedia\EntryDiscussion\EntryOpponentCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Encyclopedia\EntryDiscussion\EntryOpponentCreatedNotification;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\EntryReview;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryDiscussion\EntryDiscussionEvent;
use App\Home\Encyclopedia\EntryDynamic;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryOpponentCreatedListener
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
     * @param  EntryOpponentCreatedEvent  $event
     * @return void
     */
    public function handle(EntryOpponentCreatedEvent $event)
    {
        //词条反对的讨论创建后，仅通知协作组成员和关注词条用户，不必通知关注该分类的用户
        $opp = $event->entryOpponent;
        $entry = Entry::find($opp->eid);
        $cooperation = EntryCooperation::find($entry->cooperation_id);
        // 添加事件到用户动态
        $behavior = '发表了百科反对讨论：';
        $objectName = $opp->title;
        $objectURL = '/encyclopedia/discussion/'.$entry->id.'/'.$entry->title.'#discussionOpponent'.$opp->id;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($opp->author_id,$opp->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $entryBehavior = '新增反对讨论';
        EntryDynamic::dynamicAdd($entry->id,$entry->title,$entryBehavior,$objectName,$objectURL,$createtime);
        //发表了有效的讨论后，积分和成长值+100
        User::expAndGrowValue($opp->author_id,'100','100');
        // 添加讨论事件
        EntryDiscussionEvent::discussionEventAdd($entry->id,$opp->author_id,$opp->author,'发表了立场为[反对]的讨论内容：<'.$opp->title.'>。');

        // 词条添加热度记录
        $b_id = 48;
        EntryTemperatureRecord::recordAdd($entry->id,$opp->author_id,$b_id,$createtime);
        // 开启对协作组成员和关注词条用户的通知
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
        Notification::send($usersToNotification, new EntryOpponentCreatedNotification($opp));
    }
}
