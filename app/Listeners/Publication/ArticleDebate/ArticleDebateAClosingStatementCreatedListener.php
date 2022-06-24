<?php

namespace App\Listeners\Publication\ArticleDebate;

use App\Events\Publication\ArticleDebate\ArticleDebateAClosingStatementCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Publication\ArticleDebate\ArticleDebateAClosingStatementCreatedNotification;
use App\Notifications\Publication\ArticleDebate\ArticleDebateACSCreatedNotification;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleDebate\ArticleDebateEvent;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleDebateAClosingStatementCreatedListener
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
     * @param  ArticleDebateAClosingStatementCreatedEvent  $event
     * @return void
     */
    public function handle(ArticleDebateAClosingStatementCreatedEvent $event)
    {
        //攻方写入总结陈词内容后，需要写入词条辩论事件，并通知辩方攻方已经回应，注意如果有裁判，应通知裁判
        //添加事件到辩论事件表
        $debate = $event->articleDebate;
        $article = Article::find($debate->aid);
        ArticleDebateEvent::debateEventAdd($debate->id,$debate->Aauthor_id,$debate->Aauthor,'在发起的攻辩:['.$debate->title.']发表了攻方总结陈词。'); 
        //发表了有效的总结陈词后，积分和成长值+100
        User::expAndGrowValue($debate->Aauthor_id,'100','100');
        // 写入用户动态
        $behavior = '写入攻方总结陈词，在发起的攻辩：';
        $objectName = $debate->title;
        $objectURL = '/publication/debate/'.$article->id.'/'.$article->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($debate->Aauthor_id,$debate->Aauthor,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加热度记录
        $b_id = 60;
        ArticleTemperatureRecord::recordAdd($debate->aid,$debate->Aauthor_id,$b_id,$createtime);
        // 发送通知给攻方用户
        User::find($debate->Bauthor_id)->notify(new ArticleDebateAClosingStatementCreatedNotification($debate));
        if($debate->referee_id){
            User::find($debate->referee_id)->notify(new ArticleDebateACSCreatedToRefereeNotification($debate));
        }
    }
}
