<?php

namespace App\Listeners\Vote;

use App\Events\Vote\VoteCreatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Vote\Vote;
use App\Models\User;
use Carbon\Carbon;
use App\Home\UserDynamic;
use App\Home\Announcement;

class VoteCreatedListener
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
     * @param  VoteCreatedEvent  $event
     * @return void
     */
    public function handle(VoteCreatedEvent $event)
    {
        // 添加事件到用户动态
        $vote = $event->vote;
        $behavior = '开启了'.($vote->type==1?'[单选]':'[多选]').'投票：《'.$vote->title.'》。';
        $objectName = $vote->title;
        $objectURL = '/vote/particular/'.$vote->id.'/'.$vote->title;
        $fromName = '投票';
        $fromURL = '/vote';
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($vote->initiate_id,User::find($vote->initiate_id)->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 发布公告5代表创建
        Announcement::announcementAdd('8','5','投票<'.$vote->title.'>已经开启。',$objectURL,$createtime);
    }
}
