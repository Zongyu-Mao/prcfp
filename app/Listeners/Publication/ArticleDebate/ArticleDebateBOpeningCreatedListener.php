<?php

namespace App\Listeners\Publication\ArticleDebate;

use App\Events\Publication\ArticleDebate\ArticleDebateBOpeningCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Publication\ArticleDebate\ArticleDebateBOpeningCreatedNotification;
use App\Notifications\Publication\ArticleDebate\ArticleDebateBOpeningCreatedToRefereeNotification;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleDebate\ArticleDebateEvent;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleDebateBOpeningCreatedListener
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
     * @param  ArticleDebateBOpeningCreatedEvent  $event
     * @return void
     */
    public function handle(ArticleDebateBOpeningCreatedEvent $event)
    {
        //辩方开篇陈词后，需要写入辩论事件，并通知攻方辩方已经回应
        //添加事件到辩论事件表
        $debate = $event->articleDebate;
        $article = Article::find($debate->aid);
        ArticleDebateEvent::debateEventAdd($debate->id,$debate->Bauthor_id,$debate->Bauthor,'回应了'.$debate->Aauthor.'发起的攻辩:['.$debate->title.']；发表了辩方开篇陈词'); 
        //发表了有效的立论及陈词后，积分和成长值+100
        User::expAndGrowValue($debate->Bauthor_id,'100','100');
        // 写入用户动态
        $behavior = '写入辩方陈词，回应攻辩：';
        $objectName = $debate->title;
        $objectURL = '/publication/debate/'.$article->id.'/'.$article->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($debate->Bauthor_id,$debate->Bauthor,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加热度记录
        $b_id = 61;
        ArticleTemperatureRecord::recordAdd($debate->aid,$debate->Bauthor_id,$b_id,$createtime);
        // 发送通知给发布辩论用户
        User::find($debate->Aauthor_id)->notify(new ArticleDebateBOpeningCreatedNotification($debate));
        if($debate->referee_id){
            User::find($debate->referee_id)->notify(new ArticleDebateBOpeningCreatedToRefereeNotification($debate));
        }
    }
}
