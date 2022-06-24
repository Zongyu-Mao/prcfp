<?php

namespace App\Listeners\Publication\ArticleCooperation;

use App\Events\Publication\ArticleCooperation\ArticleCooperationMemberQuittedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\ArticleCooperation\ArticleCooperationEvent;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\Publication\Article;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Home\Cooperation\ArticleContributeValue;

class ArticleCooperationMemberQuittedListener
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
     * @param  ArticleCooperationMemberQuittedEvent  $event
     * @return void
     */
    public function handle(ArticleCooperationMemberQuittedEvent $event)
    {
        // 组员退出后，写入事件，并发送通知给被请出组员
        $cooperationUser = $event->articleCooperationUser;
        $cooperation = ArticleCooperation::find($cooperationUser->cooperation_id);
        $article = Article::find($cooperation->aid);
        $crew = User::find($cooperationUser->user_id);
        ArticleContributeValue::contributeDelete($cooperationUser->cooperation_id,$cooperationUser->user_id);
        ArticleCooperationEvent::cooperationEventAdd($cooperation->id,$crew->id,$crew->username,'退出协作计划。');
        // 写入用户动态
        $behavior = '退出著作协作计划：';
        $objectName = $cooperation->title;
        $objectURL = '/publication/cooperation/'.$article->id.'/'.$article->title;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($crew->id,$crew->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 试卷添加热度记录
        $b_id = 17;
        ArticleTemperatureRecord::recordAdd($article->id,$crew->id,$b_id,$createtime);
    }
}
