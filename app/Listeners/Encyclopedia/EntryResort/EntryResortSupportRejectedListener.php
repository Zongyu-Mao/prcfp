<?php

namespace App\Listeners\Encyclopedia\EntryResort;

use App\Events\Encyclopedia\EntryResort\EntryResortSupportRejectedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Encyclopedia\EntryResort\EntryResortSupportRejectedNotification;
use App\Home\Encyclopedia\EntryResort;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryResort\EntryResortEvent;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryResortSupportRejectedListener
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
     * @param  EntryResortSupportRejectedEvent  $event
     * @return void
     */
    public function handle(EntryResortSupportRejectedEvent $event)
    {
        //对帮助的拒绝，只需要通知帮助者即可
        $resort = $event->entryResort;
        $entry = Entry::find($resort->eid);
        $parentResort = EntryResort::find($resort->pid);
        //帮助被拒绝，安慰积分和成长值+10
        User::expAndGrowValue($resort->author_id,'10','10');
        EntryResortEvent::resortEventAdd($resort->eid,$parentResort->author_id,$parentResort->author,'拒绝了对<'.$parentResort->title.'>的帮助内容：<'.$resort->title.'>。');
        // 添加到用户动态
        $createtime = Carbon::now();
        $behavior = '拒绝了帮助：';
        $objectName = $resort->title;
        $objectURL = '/encyclopedia/resort/'.$entry->id.'/'.$entry->title.'#resort'.$resort->id;
        $fromName = '词条求助：'.$parentResort->title;
        $fromURL = '/encyclopedia/resort/'.$entry->id.'/'.'#resort'.$parentResort->id;
        UserDynamic::dynamicAdd($parentResort->author_id,$parentResort->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 词条添加热度记录
        $b_id = 43;
        EntryTemperatureRecord::recordAdd($entry->id,$resort->author_id,$b_id,$createtime);
        // 通知求助者
        User::find($resort->author_id)->notify(new EntryResortSupportRejectedNotification($resort));
    }
}
