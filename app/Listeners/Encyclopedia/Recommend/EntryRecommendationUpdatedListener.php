<?php

namespace App\Listeners\Encyclopedia\Recommend;

use App\Events\Encyclopedia\Recommend\EntryRecommendationUpdatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Encyclopedia\Recommend\EntryRecommendRecord;

class EntryRecommendationUpdatedListener
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
     * @param  EntryRecommendationUpdatedEvent  $event
     * @return void
     */
    public function handle(EntryRecommendationUpdatedEvent $event)
    {
        //更新推荐记录
        $rec = $event->entryRecommendation;
        $createtime = $rec->updated_at;
        EntryRecommendRecord::recordAdd($rec->cid,$rec->eid,$createtime);
    }
}
