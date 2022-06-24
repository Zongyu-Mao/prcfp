<?php

namespace App\Listeners\Publication\ArticleCooperation;

use App\Events\Publication\ArticleCooperation\ArticleCooperationMemberJoinedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Publication\ArticleCooperation\ArticleCooperationEvent;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\Article\ArticleDynamic;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\Publication\Article;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Home\Cooperation\ArticleContributeValue;

class ArticleCooperationMemberJoinedListener
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
     * @param  ArticleCooperationMemberJoinedEvent  $event
     * @return void
     */
    public function handle(ArticleCooperationMemberJoinedEvent $event)
    {
        // 成功写入协作成员后，触发事件：协作事件，著作动态，用户动态；此处暂时不产生通知
        $ecu = $event->articleCooperationUser;
        $cooperation = ArticleCooperation::find($ecu->cooperation_id);
        $article = Article::find($cooperation->aid);
        $user = User::find($ecu->user_id);
        $creatime = Carbon::now();
        // 写入贡献表
        ArticleContributeValue::contributeAdd($ecu->cooperation_id,$ecu->user_id,0);
        // 写入协作事件
        ArticleCooperationEvent::cooperationEventAdd($cooperation->id,$user->id,$user->username,'成功加入协作小组，大家合作愉快。');
        // 添加事件到用户动态
        $behavior = '加入了著作协作计划：';
        $objectName = $cooperation->title;
        $objectURL = '/publication/cooperation/'.$article->id.'/'.$article->title;
        $fromName = '著作'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($user->id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $articleBehavior = '新增协作计划成员：['.$user->username.']';
        ArticleDynamic::dynamicAdd($article->id,$article->title,$articleBehavior,$objectName,$objectURL,$createtime);
        // 词条添加热度记录
        $b_id = 16;
        ArticleTemperatureRecord::recordAdd($article->id,$user->id,$b_id,$createtime);
    }
}
