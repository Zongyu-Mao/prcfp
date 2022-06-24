<?php

namespace App\Listeners\Publication\ArticleDiscussion;

use App\Events\Publication\ArticleDiscussion\ArticleDiscussionRepliedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Publication\ArticleDiscussion\ArticleDiscussionRepliedNotification;
use App\Home\Publication\ArticleDiscussion;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleDiscussion\ArticleDiscussionEvent;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleDiscussionRepliedListener
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
     * @param  ArticleDiscussionRepliedEvent  $event
     * @return void
     */
    public function handle(ArticleDiscussionRepliedEvent $event)
    {
        // 普通讨论被回复后，仅通知讨论的作者
        $discussion = $event->articleDiscussion;
        $article = Article::find($discussion->aid);
        $parentDiscussion = ArticleDiscussion::find($discussion->pid);
        // 添加事件到用户动态
        $behavior = '回复著作普通讨论：';
        $objectName = $discussion->title;
        $objectURL = '/publication/discussion/'.$article->id.'/'.$article->title.'#discussion'.$discussion->id;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($discussion->author_id,$discussion->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        //讨论被回复后，回复者的积分和成长值+10
        User::expAndGrowValue($discussion->author_id,'10','10');
        // 添加讨论事件
        ArticleDiscussionEvent::discussionEventAdd($article->id,$discussion->author_id,$discussion->author,'回复了['.$parentDiscussion->author.']提出的讨论内容<'.$parentDiscussion->title.'>。');
        // 添加热度记录
        $b_id = 55;
        ArticleTemperatureRecord::recordAdd($discussion->aid,$discussion->author_id,$b_id,$createtime);
        // 给反对作者发送通知
        User::find($parentDiscussion->author_id)->notify(new ArticleDiscussionRepliedNotification($discussion));
    }
}
