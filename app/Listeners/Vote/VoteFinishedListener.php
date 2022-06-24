<?php

namespace App\Listeners\Vote;

use App\Events\Vote\VoteFinishedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Vote\Vote;
use App\Models\User;
use Carbon\Carbon;
use App\Home\UserDynamic;
use App\Home\Announcement;

class VoteFinishedListener
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
     * @param  VoteFinishedEvent  $event
     * @return void
     */
    public function handle(VoteFinishedEvent $event)
    {
        // 添加事件到用户动态
        $vote = $event->vote;
        $behavior = '结束了'.($vote->type==1?'[单选]':'[多选]').'投票：《'.$vote->title.'》。';
        $objectName = $vote->title;
        $objectURL = '/vote/particular/'.$vote->id.'/'.$vote->title;
        $fromName = '投票';
        $fromURL = '/vote';
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($vote->initiate_id,User::find($vote->initiate_id)->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 发布公告，8代表投票,8代表关闭
        Announcement::announcementAdd('8','8','投票'.$vote->title.'>已经关闭。',$objectURL,$createtime);
    }
}
