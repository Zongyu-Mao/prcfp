<?php

namespace App\Listeners\Encyclopedia\EntryReview;

use App\Events\Encyclopedia\EntryReview\EntryReviewAdvisementAcceptedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Encyclopedia\EntryReview\EntryReviewAdvisementAcceptedNotification;
use App\Home\Encyclopedia\EntryReview;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryReview\EntryReviewEvent;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryReviewAdvisementAcceptedListener
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
     * @param  EntryReviewAdvisementAcceptedEvent  $event
     * @return void
     */
    public function handle(EntryReviewAdvisementAcceptedEvent $event)
    {
        //评审计划建议被小组接受后，通知原作者
        $advise = $event->entryReviewAdvise;
        $entryReview = EntryReview::find($advise->rid);
        $entry = Entry::find($entryReview->eid);
        // 添加事件到用户动态
        $behavior = '接受评审计划建议：';
        $objectName = $advise->title;
        $objectURL = '/encyclopedia/review/'.$entry->id.'/'.$entry->title.'#reviewAdvise'.$advise->id;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($advise->recipient_id,$advise->recipient,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        //积分和成长值+10
        User::expAndGrowValue($advise->recipient_id,'10','10');
        EntryReviewEvent::reviewEventAdd($advise->rid,$advise->recipient_id,$advise->recipient,'接受了'.$advise->author.'的建议评论。');
        // 词条添加热度记录
        $b_id = 31;
        EntryTemperatureRecord::recordAdd($entry->id,$advise->recipient_id,$b_id,$createtime);
        // 通知被回复作者
        User::find($advise->author_id)->notify(new EntryReviewAdvisementAcceptedNotification($advise));
    }
}
