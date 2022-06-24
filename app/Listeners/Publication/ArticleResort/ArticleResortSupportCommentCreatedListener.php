<?php

namespace App\Listeners\Publication\ArticleResort;

use App\Events\Publication\ArticleResort\ArticleResortSupportCommentCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Publication\ArticleResort\ArticleResortSupportCommentCreatedNotification;
use App\Home\Publication\ArticleResort\ArticleResortSupportComment;
use App\Home\Publication\ArticleResort;
use App\Home\Publication\ArticleResort\ArticleResortEvent;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use Carbon\Carbon;
use App\Models\User;

class ArticleResortSupportCommentCreatedListener
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
     * @param  ArticleResortSupportCommentCreatedEvent  $event
     * @return void
     */
    public function handle(ArticleResortSupportCommentCreatedEvent $event)
    {
        //原则上来说，对求助话题的帮助，只需要通知求助者即可,该comment是普通评论
        $comment = $event->articleResortSupportComment;
        $resort = ArticleResort::find($comment->resortId);
        $createtime = Carbon::now();
        ArticleResortEvent::resortEventAdd($resort->aid,$comment->author_id,$comment->getAuthor->username,'发布了帮助内容回复：<'.$comment->title.'>。');
        //积分和成长值+50
        User::expAndGrowValue($comment->author_id,'10','10');
        // 不需要用户动态
        // 添加热度记录
        $b_id = 44;
        ArticleTemperatureRecord::recordAdd($resort->aid,$comment->author_id,$b_id,$createtime);
        // 通知被回复用户
        if($comment->pid == 0){
            // 如果是pid为0，需要回复resort的作者
            $user_id = ArticleResort::find($comment->resortId)->author_id;
        }else{
            $user_id = ArticleResortSupportComment::find($comment->pid)->author_id;
        }
        User::find($user_id)->notify(new ArticleResortSupportCommentCreatedNotification($comment));
    }
}
