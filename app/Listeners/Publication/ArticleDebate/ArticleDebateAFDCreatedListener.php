<?php

namespace App\Listeners\Publication\ArticleDebate;

use App\Events\Publication\ArticleDebate\ArticleDebateAFDCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Publication\ArticleDebate\ArticleDebateAFDCreatedNotification;
use App\Notifications\Publication\ArticleDebate\ArticleDebateAFDCreatedToRefereeNotification;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleDebate\ArticleDebateEvent;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleDebateAFDCreatedListener
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
     * @param  ArticleDebateAFDCreatedEvent  $event
     * @return void
     */
    public function handle(ArticleDebateAFDCreatedEvent $event)
    {
        //攻方写入自由辩论内容后，需要写入词条辩论事件，并通知辩方攻方已经回应
        //添加事件到辩论事件表
        $debate = $event->articleDebate;
        $article = Article::find($debate->aid);
        ArticleDebateEvent::debateEventAdd($debate->id,$debate->Aauthor_id,$debate->Aauthor,'在其发起的攻辩:['.$debate->title.']发表了攻方自由辩论。'); 
        //发表了有效的立论及陈词后，积分和成长值+100
        User::expAndGrowValue($debate->Aauthor_id,'100','100');
        // 写入用户动态
        $behavior = '写入攻方自由辩论，在发起的攻辩：';
        $objectName = $debate->title;
        $objectURL = '/publciation/debate/'.$article->id.'/'.$article->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publciation/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($debate->Aauthor_id,$debate->Aauthor,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加热度记录
        $b_id = 60;
        ArticleTemperatureRecord::recordAdd($debate->aid,$debate->Aauthor_id,$b_id,$createtime);
        // 发送通知给辩方用户
        User::find($debate->Bauthor_id)->notify(new ArticleDebateAFDCreatedNotification($debate));
        if($debate->referee_id){
            User::find($debate->referee_id)->notify(new ArticleDebateAFDCreatedToRefereeNotification($debate));
        }
    }
}
