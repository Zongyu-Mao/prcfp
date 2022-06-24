<?php

namespace App\Listeners\Publication\ArticleDebate\ArticleDebateReferee;

use App\Events\Publication\ArticleDebate\ArticleDebateReferee\ArticleDebateAnalyseUpdatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleDebate\ArticleDebateEvent;
use App\Notifications\Publication\ArticleDebate\ArticleDebateReferee\ArticleDebateAnalyseUpdatedNotification;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleDebateAnalyseUpdatedListener
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
     * @param  ArticleDebateAnalyseUpdatedEvent  $event
     * @return void
     */
    public function handle(ArticleDebateAnalyseUpdatedEvent $event)
    {
        //裁判分析的更新，只需要写入事件，写入用户动态，通知攻辩双方
        //添加事件到辩论事件表
        $debate = $event->articleDebate;
        $article = Article::find($debate->aid);
        ArticleDebateEvent::debateEventAdd($debate->id,$debate->referee_id,$debate->referee,'更新了裁判分析。'); 
        // 添加事件到用户动态
        $behavior = '发表/更新了裁判分析，在攻辩：';
        $objectName = $debate->title;
        $objectURL = '/publication/debate/'.$article->id.'/'.$article->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($debate->referee_id,$debate->referee,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加热度记录
        $b_id = 63;
        if(ArticleTemperatureRecord::where([['aid',$exam->id],['user_id',$debate->referee_id],['behavior_id',$be_id]])->count() < 3){
             ArticleTemperatureRecord::recordAdd($debate->aid,$debate->referee_id,$b_id,$createtime);
        }
        // 发送通知给辩论攻方
        User::find($debate->Aauthor_id)->notify(new ArticleDebateAnalyseUpdatedNotification($debate));
        // 发送通知给辩论辩方
        User::find($debate->Bauthor_id)->notify(new ArticleDebateAnalyseUpdatedNotification($debate));
    }
}
