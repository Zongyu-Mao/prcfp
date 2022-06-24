<?php

namespace App\Listeners\Encyclopedia\EntryReview;

use App\Events\Encyclopedia\EntryReview\EntryReviewDiscussionRepliedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Encyclopedia\EntryReview\EntryReviewDiscussionRepliedNotification;
use App\Home\Encyclopedia\EntryReview;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryReview\EntryReviewEvent;
use App\Home\Encyclopedia\EntryReview\EntryReviewDiscussion;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class EntryReviewDiscussionRepliedListener
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
     * @param  EntryReviewDiscussionRepliedEvent  $event
     * @return void
     */
    public function handle(EntryReviewDiscussionRepliedEvent $event)
    {
        //评论回复仅需通知被回复评论作者即可
        $discussion = $event->entryReviewDiscussion;
        $entryReview = EntryReview::find($discussion->rid);
        $entry = Entry::find($entryReview->eid);
        $parentDiscussion = EntryReviewDiscussion::find($discussion->pid);
        // 添加事件到用户动态
        $behavior = '回复了评审计划支持/中立意见：';
        $objectName = $entryReview->title;
        $objectURL = '/encyclopedia/review/'.$entry->id.'/'.$entry->title.'#reviewDiscussion'.$discussion->id;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($discussion->author_id,$discussion->getAuthor->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        //发表了有效的评审意见后，积分和成长值+50
        User::expAndGrowValue($discussion->author_id,'10','10');
        EntryReviewEvent::reviewEventAdd($discussion->rid,$discussion->author_id,$discussion->getAuthor->username,'回复了'.$parentDiscussion->getAuthor->username.'的评论。');
        // 词条添加热度记录
        $b_id = 28;
        EntryTemperatureRecord::recordAdd($entry->id,$discussion->author_id,$b_id,$createtime);
        // 通知被回复作者
        User::find($parentDiscussion->author_id)->notify(new EntryReviewDiscussionRepliedNotification($discussion));
    }
}
