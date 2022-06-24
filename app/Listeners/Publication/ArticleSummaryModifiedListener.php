<?php

namespace App\Listeners\Publication;

use App\Events\Publication\ArticleSummaryModifiedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Publication\ArticleCooperation\ArticleCooperationEvent;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\Article\ArticleDynamic;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ArticleSummaryModifiedListener
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
     * @param  ArticleSummaryModifiedEvent  $event
     * @return void
     */
    public function handle(ArticleSummaryModifiedEvent $event)
    {
        // 添加事件到用户动态、著作动态和协作事件
        $cooperation = ArticleCooperation::find($event->article->cooperation_id);
        $behavior = '编辑了著作摘要：';
        $objectName = $event->article->title;
        $objectURL = '/publication/reading/'.$event->article->id.'/'.$event->article->title;
        $fromName = '著作：'.$event->article->title;
        $fromURL = '/publication/cooperation/'.$event->article->id.'/'.$event->article->title;
        $createtime = Carbon::now();
        $user = auth('api')->user();
        UserDynamic::dynamicAdd($user->id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $articleBehavior = '著作摘要已经修改';
        ArticleDynamic::dynamicAdd($event->article->id,$event->article->title,$articleBehavior,$objectName,$objectURL,$createtime);
        // 添加协作事件
        ArticleCooperationEvent::cooperationEventAdd($cooperation->id,$user->id,$user->username,'修改了著作摘要。');
    }
}
