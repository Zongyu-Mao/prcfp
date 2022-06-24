<?php

namespace App\Listeners\Encyclopedia\EntryResort;

use App\Events\Encyclopedia\EntryResort\EntryResortSupportCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Encyclopedia\EntryResort\EntryResortSupportCreatedNotification;
use App\Home\Encyclopedia\EntryResort;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryResort\EntryResortEvent;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryResortSupportCreatedListener
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
     * @param  EntryResortSupportCreatedEvent  $event
     * @return void
     */
    public function handle(EntryResortSupportCreatedEvent $event)
    {
        //原则上来说，对求助话题的帮助，只需要通知求助者即可
        $resort = $event->entryResort;
        $entry = Entry::find($resort->eid);
        $parentResort = EntryResort::find($resort->pid);
        $createtime = Carbon::now();
        //积分和成长值+50
        User::expAndGrowValue($resort->author_id,'50','50');
        EntryResortEvent::resortEventAdd($resort->eid,$resort->author_id,$resort->author,'发布了对<'.$parentResort->title.'>的帮助内容：<'.$resort->title.'>。');
        // 添加到用户动态
        $behavior = '发布了帮助：';
        $objectName = $resort->title;
        $objectURL = '/encyclopedia/resort/'.$entry->id.'/'.$entry->title.'#resort'.$resort->id;
        $fromName = '词条求助：'.$parentResort->title;
        $fromURL = '/encyclopedia/resort/'.$entry->id.'/'.'#resort'.$parentResort->id;
        UserDynamic::dynamicAdd($resort->author_id,$resort->author,$behavior,$objectName,$objectURL,$fromName,$fromURL, $createtime);
        // 词条添加热度记录
        $b_id = 41;
        EntryTemperatureRecord::recordAdd($entry->id,$resort->author_id,$b_id,$createtime);
        // 通知求助者
        User::find($parentResort->author_id)->notify(new EntryResortSupportCreatedNotification($resort));
    }
}
