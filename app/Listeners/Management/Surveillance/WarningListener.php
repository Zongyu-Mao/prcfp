<?php

namespace App\Listeners\Management\Surveillance;

use App\Events\Management\Surveillance\WarningEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use App\Home\Encyclopedia\EntryDynamic;
use App\Notifications\Management\Surveillance\EntryWarned;
use App\Home\Encyclopedia\EntryCooperation;
use Carbon\Carbon;
use App\Models\User;

class WarningListener
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
     * @param  WarningEvent  $event
     * @return void
     */
    public function handle(WarningEvent $event)
    {
        $warn = $event->surveillanceWarning;
        // 获取词条的关注用户
        $entry = $warn->content;
        $objectName = $entry->title;
        $status = $warn->status;
        if($status==0) {
            $behavior = '主内容已被警示。';
        }elseif($status==1) {
            $behavior = '主内容申请警示撤销。';
        }if($status==2) {
            $behavior ='主内容警示已撤销。';
        }
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
        // warn 要通知协作组
        $usersToNotification = User::whereIn('id',$crewArr)->get();
        if($warn->status==0||$warn->status==2)Notification::send($usersToNotification, new EntryWarned($warn));
        if($warn->status==1)User::find($warn->user_id)->notify(new EntryWarned($warn));
    }
}
