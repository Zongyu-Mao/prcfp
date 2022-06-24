<?php

namespace App\Listeners\Publication\ArticleCooperation;

use App\Events\Publication\ArticleCooperation\ArticleCooperationMessageRepliedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Publication\ArticleCooperation\ArticleCooperationMessage\ArticleCooperationMessageRepliedNotification;
use App\Home\Publication\ArticleCooperation\ArticleCooperationMessage;
use App\Home\Publication\ArticleCooperation\ArticleCooperationEvent;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use Carbon\Carbon;
use App\Models\User;

class ArticleCooperationMessageRepliedListener
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
     * @param  ArticleCooperationMessageRepliedEvent  $event
     * @return void
     */
    public function handle(ArticleCooperationMessageRepliedEvent $event)
    {
        //回复还要找到原作者并通知，写入协作事件
        // 找到回复
        $msg = $event->articleCooperationMessage;
        $message = ArticleCooperationMessage::find($msg->pid);
        $user = User::find($message->author_id);
        // 添加协作事件
        ArticleCooperationEvent::cooperationEventAdd($msg->cooperation_id,$msg->author_id,$msg->author,'回复了'.$message->author.'在协作计划发表的讨论留言。');
        // 添加热度记录
        $aid = ArticleCooperation::find($msg->cooperation_id)->aid;
        $b_id = 21;
        ArticleTemperatureRecord::recordAdd($aid,$message->author_id,$b_id,Carbon::now());
        // 通知用户留言被回复
        User::find($message->author_id)->notify(new ArticleCooperationMessageRepliedNotification($msg));
        
    }
}
