<?php

namespace App\Listeners\Publication\Article;

use App\Events\Publication\Article\ArticleContentDeletedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Publication\Article\ArticleContent;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\ArticleCooperation\ArticleCooperationEvent;
use App\Home\Publication\Article\ArticleDynamic;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ArticleContentDeletedListener
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
     * @param  ArticleContentDeletedEvent  $event
     * @return void
     */
    public function handle(ArticleContentDeletedEvent $event)
    {
        // 内容的修改不会通知协作组，只在协作计划事件中写入
        $cooperation = ArticleCooperation::where([['aid',$event->articleContent->aid],['status','0']])->first();
        $article = Article::find($event->articleContent->aid);
        $article->increment('edit_number');
        $user = auth('api')->user();
        // 添加事件到用户动态
        $behavior = '删除了著作《'.$article->title.'》正文原第'.$event->articleContent->sort.'章节内容：';
        $objectName = $article->title;
        $objectURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($user->id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到著作动态
        $articleBehavior = '正文内容原第'.$event->articleContent->sort.'章节已经删除：';
        ArticleDynamic::dynamicAdd($article->id,$article->title,$articleBehavior,$objectName,$objectURL,$createtime);
        // 添加协作事件
        ArticleCooperationEvent::cooperationEventAdd($cooperation->id,$user->id,$user->username,'删除了原第'.$event->articleContent->sort.'章节内容：<'.$event->articleContent->title.'>。');
    }
}
