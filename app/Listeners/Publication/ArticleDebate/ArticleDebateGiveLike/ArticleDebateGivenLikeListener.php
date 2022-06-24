<?php

namespace App\Listeners\Publication\ArticleDebate\ArticleDebateGiveLike;

use App\Events\Publication\ArticleDebate\ArticleDebateGiveLike\ArticleDebateGivenLikeEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Publication\ArticleDebate;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleDebate\ArticleDebateEvent;
use App\Notifications\Publication\ArticleDebate\ArticleDebateGiveLike\ArticleDebateGivenLikeNotification;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleDebateGivenLikeListener
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
     * @param  ArticleDebateGivenLikeEvent  $event
     * @return void
     */
    public function handle(ArticleDebateGivenLikeEvent $event)
    {
        //用户点赞后，记录事件，发送通知给被点赞方
        $record = $event->articleDebateStarRecord;
        $debate = ArticleDebate::find($record->debate_id);
        $article = Article::find($debate->aid);
        // 判断立场并更新辩论表的点赞数
        if($record->star == '0'){
            $standpoint = '送了一颗红星星给';
        }elseif($record->star == '1'){
            $standpoint = '送了一颗黑星星给';
        }
        // 判断对象
        if($record->object == '0'){
            $starObject = '攻方。';
            $notify_id = $debate->Aauthor_id;
        }elseif($record->object == '1'){
            $starObject = '辩方。';
            $notify_id = $debate->Bauthor_id;
        }elseif($record->object == '2'){
            $starObject = '裁判。';
            $notify_id = $debate->referee_id;
        }
        //添加事件到辩论事件表
        ArticleDebateEvent::debateEventAdd($record->debate_id,$record->user_id,$record->username,$standpoint.$starObject); 
        // 添加事件到用户动态
        $behavior = $standpoint.$starObject.'在攻辩：';
        $objectName = $debate->title;
        $objectURL = '/publication/debate/'.$article->id.'/'.$article->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($record->user_id,$record->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 发送通知给被点赞方
        User::find($notify_id)->notify(new ArticleDebateGivenLikeNotification($record));
    }
}
