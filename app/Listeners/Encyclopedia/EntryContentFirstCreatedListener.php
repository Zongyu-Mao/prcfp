<?php

namespace App\Listeners\Encyclopedia;

use App\Events\Encyclopedia\EntryContentFirstCreatedEvent;
use App\Notifications\Encyclopedia\Entry\EntryContentFirstCreatedNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryContentFirstCreatedListener
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
     * @param  EntryContentFirstCreatedEvent  $event
     * @return void
     */
    public function handle(EntryContentFirstCreatedEvent $event)
    {
        $user = User::find($event->entry->lasteditor_id);
        $user->notify(new EntryContentFirstCreatedNotification($event->entry));
        // 添加事件到用户动态
        $behavior = '添加了词条正文：';
        $objectName = $event->entry->title;
        $objectURL = '/encyclopedia/reading/'.$event->entry->id.'/'.$event->entry->title;
        $fromName = '词条：'.$event->entry->title;
        $fromURL = '/encyclopedia/reading/'.$event->entry->id.'/'.$event->entry->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($user->id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
    }
}
