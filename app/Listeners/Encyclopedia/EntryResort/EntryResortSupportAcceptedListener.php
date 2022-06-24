<?php

namespace App\Listeners\Encyclopedia\EntryResort;

use App\Events\Encyclopedia\EntryResort\EntryResortSupportAcceptedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Encyclopedia\EntryResort\EntryResortSupportAcceptedNotification;
use App\Notifications\Encyclopedia\EntryResort\EntryResortSupportUselessNotification;
use App\Notifications\Encyclopedia\EntryResort\EntryResortSupportAcceptedToUserNotification;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryResort;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\EntryResort\EntryResortEvent;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryResortSupportAcceptedListener
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
     * @param  EntryResortSupportAcceptedEvent  $event
     * @return void
     */
    public function handle(EntryResortSupportAcceptedEvent $event)
    {
        //帮助被采纳，通知帮助者和小组成员
        $resort = $event->entryResort;
        $entry = Entry::find($resort->eid);
        $parentResort = EntryResort::find($resort->pid);
        $cooperation = EntryCooperation::find($entry->cooperation_id);
        // 帮助被采纳后，求助更改状态为已解决，帮助更改状态为已采纳，其余帮助均更改为失效。
        EntryResort::where('id',$resort->pid)->update(['status'=>'1']);
        $elseSupports = EntryResort::where([['pid',$resort->pid],['status','0']])->get();
        if(count($elseSupports)){
            foreach($elseSupports as $value){
                EntryResort::where('id',$value->id)->update(['status'=>'3']);
                User::find($value->author_id)->notify(new EntryResortSupportUselessNotification($resort));
            }
        }
        $createtime = Carbon::now();
        //接受了帮助后，操作者积分和成长值+10
        User::expAndGrowValue($resort->author_id,'10','10');
        // 添加求助事件
        EntryResortEvent::resortEventAdd($resort->eid,$parentResort->author_id,$parentResort->author,'接受了'.$resort->author.'的帮助内容:<'.$resort->title.'>。');
        // 添加到用户动态
        $behavior = '采纳了帮助：';
        $objectName = $resort->title;
        $objectURL = '/encyclopedia/resort/'.$entry->id.'/'.$entry->title.'#resort'.$resort->id;
        $fromName = '词条求助：'.$parentResort->title;
        $fromURL = '/encyclopedia/resort/'.$entry->id.'/'.'#resort'.$parentResort->id;
        UserDynamic::dynamicAdd($parentResort->author_id,$parentResort->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 词条添加热度记录
        $b_id = 42;
        EntryTemperatureRecord::recordAdd($entry->id,$resort->author_id,$b_id,$createtime);
        // 通知原反对作者被接受
        User::find($resort->author_id)->notify(new EntryResortSupportAcceptedToUserNotification($resort));
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
        // 合并协作组
        $users = array_unique($crewArr);
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyEntryAdd($result));
        Notification::send($usersToNotification, new EntryResortSupportAcceptedNotification($resort));
    }
}
