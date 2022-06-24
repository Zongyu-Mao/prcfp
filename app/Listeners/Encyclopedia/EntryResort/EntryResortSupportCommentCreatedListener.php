<?php

namespace App\Listeners\Encyclopedia\EntryResort;

use App\Events\Encyclopedia\EntryResort\EntryResortSupportCommentCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Encyclopedia\EntryResort\EntryResortSupportCommentCreatedNotification;
use App\Home\Encyclopedia\EntryResort\EntryResortSupportComment;
use App\Home\Encyclopedia\EntryResort;
use App\Home\Encyclopedia\EntryResort\EntryResortEvent;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Personnel\Behavior;
use Carbon\Carbon;
use App\Models\User;

class EntryResortSupportCommentCreatedListener
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
     * @param  EntryResortSupportCommentCreatedEvent  $event
     * @return void
     */
    public function handle(EntryResortSupportCommentCreatedEvent $event)
    {
        //原则上来说，对求助话题的帮助，只需要通知求助者即可,该comment是普通评论
        $comment = $event->entryResortSupportComment;
        $resort = EntryResort::find($comment->resortId);
        $createtime = Carbon::now();
        EntryResortEvent::resortEventAdd($resort->eid,$comment->author_id,$comment->author,'发布了帮助内容回复：<'.$comment->title.'>。');
        //积分和成长值+50
        User::expAndGrowValue($comment->author_id,'10','10');
        // 不需要用户动态
        // 词条添加热度记录
        $b_id = 44;
        EntryTemperatureRecord::recordAdd($comment->eid,$comment->author_id,$b_id,$createtime);
        // 通知被回复用户
        if($comment->pid == 0){
            // 如果是pid为0，需要回复resort的作者
            $user_id = EntryResort::find($comment->resortId)->author_id;
        }else{
            $user_id = EntryResortSupportComment::find($comment->pid)->author_id;
        }
        User::find($user_id)->notify(new EntryResortSupportCommentCreatedNotification($comment));
    }
}
