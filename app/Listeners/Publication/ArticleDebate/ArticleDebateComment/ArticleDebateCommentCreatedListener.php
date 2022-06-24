<?php

namespace App\Listeners\Publication\ArticleDebate\ArticleDebateComment;

use App\Events\Publication\ArticleDebate\ArticleDebateComment\ArticleDebateCommentCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Publication\ArticleDebate\ArticleDebateComment\ArticleDebateCommentRepliedNotification;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleDebate\ArticleDebateComment;
use App\Home\Publication\ArticleDebate\ArticleDebateEvent;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use Carbon\Carbon;
use App\Models\User;

class ArticleDebateCommentCreatedListener
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
     * @param  ArticleDebateCommentCreatedEvent  $event
     * @return void
     */
    public function handle(ArticleDebateCommentCreatedEvent $event)
    {
        //
        $comment = $event->articleDebateComment;
        // $article = Article::find($comment->aid);
        $createtime = Carbon::now();
        //添加事件到辩论事件表
        ArticleDebateEvent::debateEventAdd($comment->debate_id,$comment->author_id,$comment->getAuthor->username,'发表了新的评论:<'.$comment->title.'>。');
        // 词条添加热度记录
        if($comment->pid == 0){
            $b_id = 69;
        }else{
            $b_id = 70;
            User::find(ArticleDebateComment::find($comment->pid)->author_id)->notify(new ArticleDebateCommentRepliedNotification($comment));
        }
        ArticleTemperatureRecord::recordAdd($comment->aid,$comment->author_id,$b_id,$createtime);
    }
}
