<?php

namespace App\Listeners\Publication\ArticleDebate\ArticleDebateReferee;

use App\Events\Publication\ArticleDebate\ArticleDebateReferee\ArticleDebateRefereeJoinedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleDebate\ArticleDebateEvent;
use App\Notifications\Publication\ArticleDebate\ArticleDebateReferee\ArticleDebateRefereeJoinedNotification;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleDebateRefereeJoinedListener
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
     * @param  ArticleDebateRefereeJoinedEvent  $event
     * @return void
     */
    public function handle(ArticleDebateRefereeJoinedEvent $event)
    {
        //裁判加入仅需写入辩论事件，用户动态并通知攻辩双方
        //添加事件到辩论事件表
        $debate = $event->articleDebate;
        $article = Article::find($debate->aid);
        ArticleDebateEvent::debateEventAdd($debate->id,$debate->referee_id,$debate->referee,'成为裁判'); 
        // 添加事件到用户动态
        $behavior = '成为裁判，在攻辩：';
        $objectName = $debate->title;
        $objectURL = '/publication/debate/'.$article->id.'/'.$article->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($debate->Bauthor_id,$debate->referee_id,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加热度记录
        $b_id = 62;
        ArticleTemperatureRecord::recordAdd($debate->aid,$debate->referee_id,$b_id,$createtime);
        // 发送通知给辩论攻方
        User::find($debate->Aauthor_id)->notify(new ArticleDebateRefereeJoinedNotification($debate));
        // 发送通知给辩论辩方
        User::find($debate->Bauthor_id)->notify(new ArticleDebateRefereeJoinedNotification($debate));
    }
}
