<?php

namespace App\Listeners\Publication\ArticleCooperation;

use App\Events\Publication\ArticleCooperation\ArticleCooperationMemberFiredEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\ArticleCooperation\ArticleCooperationEvent;
use App\Notifications\Publication\ArticleCooperation\ArticleCooperationMemberFiredNotification;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\Publication\Article;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Home\Cooperation\ArticleContributeValue;

class ArticleCooperationMemberFiredListener
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
     * @param  ArticleCooperationMemberFiredEvent  $event
     * @return void
     */
    public function handle(ArticleCooperationMemberFiredEvent $event)
    {
        // 组员被请出后，写入事件，并发送通知给被请出组员
        $acu = $event->articleCooperationUser;
        $cooperation = ArticleCooperation::find($acu->cooperation_id);
        $article = Article::find($cooperation->aid);
        $crew = User::find($acu->user_id);
        ArticleContributeValue::contributeDelete($acu->cooperation_id,$acu->user_id);
        ArticleCooperationEvent::cooperationEventAdd($cooperation->id,$cooperation->manage_id,$cooperation->manager,'已经请出组员<'.$crew->username.'>。');
        // 写入用户动态
        $behavior = '退出著作协作计划：';
        $objectName = $cooperation->title;
        $objectURL = '/publication/cooperation/'.$article->id.'/'.$article->title;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($crew->id,$crew->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        $crew->notify(new ArticleCooperationMemberFiredNotification($acu));
        // 添加热度记录
        $b_id = 18;
        ArticleTemperatureRecord::recordAdd($article->id,$crew->id,$b_id,$createtime);
    }
}
