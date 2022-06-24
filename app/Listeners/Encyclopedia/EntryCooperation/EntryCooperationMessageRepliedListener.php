<?php

namespace App\Listeners\Encyclopedia\EntryCooperation;

use App\Events\Encyclopedia\EntryCooperation\EntryCooperationMessageRepliedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Encyclopedia\EntryCooperation\EntryCooperationMessageRepliedNotification;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationMessage;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationEvent;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use Carbon\Carbon;
use App\Models\User;

class EntryCooperationMessageRepliedListener
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
     * @param  EntryCooperationMessageRepliedEvent  $event
     * @return void
     */
    public function handle(EntryCooperationMessageRepliedEvent $event)
    {
        //回复仅需通知留言用户即可
        $message = $event->entryCooperationMessage;
        $cooperation = EntryCooperation::find($message->cooperation_id);
        $entry = Entry::find($cooperation->eid);
        $parentMessage = EntryCooperationMessage::find($message->pid);
        User::expAndGrowValue($message->author_id,'10','10');
        // 词条添加热度记录
        $b_id = 21;
        EntryTemperatureRecord::recordAdd($entry->id,$message->author_id,$b_id,Carbon::now());
        // 添加协作事件
        EntryCooperationEvent::cooperationEventAdd($message->cooperation_id,$message->author_id,$message->author,'回复了['.$parentMessage->author.']发表的留言：<'.$message->title.'>。');
        // 通知用户留言被回复
        User::find($parentMessage->author_id)->notify(new EntryCooperationMessageRepliedNotification($message));
    }
}
