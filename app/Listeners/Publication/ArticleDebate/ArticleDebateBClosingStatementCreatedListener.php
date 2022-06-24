<?php

namespace App\Listeners\Publication\ArticleDebate;

use App\Events\Publication\ArticleDebate\ArticleDebateBClosingStatementCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Publication\ArticleDebate\ArticleDebateBClosingStatementCreatedNotification;
use App\Notifications\Publication\ArticleDebate\ArticleDebateBCSCreatedToRefereeNotification;
use App\Notifications\Publication\ArticleDebate\ArticleDebateBCSCreatedNotification;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleDebate\ArticleDebateEvent;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleDebateBClosingStatementCreatedListener
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
     * @param  ArticleDebateBClosingStatementCreatedEvent  $event
     * @return void
     */
    public function handle(ArticleDebateBClosingStatementCreatedEvent $event)
    {
        // 辩方写入总结陈词内容后，需要写入词条辩论事件，并通知攻方辩方已经回应，实际上，到此对于攻辩双方已经结束，但是并非攻辩结束，因此不需通知其余用户
        //添加事件到辩论事件表
        $debate = $event->articleDebate;
        $article = Article::find($debate->aid);
        ArticleDebateEvent::debateEventAdd($debate->id,$debate->Bauthor_id,$debate->Bauthor,'在攻辩:['.$debate->title.']发表了辩方总结陈词。'); 
        //发表了有效的总结陈词后，积分和成长值+100
        User::expAndGrowValue($debate->Bauthor_id,'100','100');
        // 写入用户动态
        $behavior = '写入辩方总结陈词，在攻辩：';
        $objectName = $debate->title;
        $objectURL = '/publication/debate/'.$article->id.'/'.$article->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($debate->Bauthor_id,$debate->Bauthor,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加热度记录
        $b_id = 61;
        ArticleTemperatureRecord::recordAdd($debate->aid,$debate->Bauthor_id,$b_id,$createtime);
        // 发送通知给攻方用户
        User::find($debate->Aauthor_id)->notify(new ArticleDebateBClosingStatementCreatedNotification($debate));
        // 如果没有裁判，这里直接要触发辩论的结算
        if($debate->referee_id){
            User::find($debate->referee_id)->notify(new ArticleDebateBCSCreatedToRefereeNotification($debate));
        }
    }
}
