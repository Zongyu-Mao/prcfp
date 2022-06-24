<?php

namespace App\Listeners\Publication\Article;

use App\Events\Publication\Article\ArticleKeywordModifiedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Publication\ArticleCooperation\ArticleCooperationEvent;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\Article\ArticleDynamic;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ArticleKeywordModifiedListener
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
     * @param  ArticleKeywordModifiedEvent  $event
     * @return void
     */
    public function handle(ArticleKeywordModifiedEvent $event)
    {
        //关键词的更改较为简单，不用写出具体关键词，仅记录修改了，写入用户动态、协作事件和著作动态
        $cooperation = ArticleCooperation::find($event->article->cooperation_id);
        $behavior = '编辑了著作关键词：';
        // 对象现在是著作，是不是要改成关键词？？？？后面再说啦暂时不考虑了
        $objectName = $event->article->title;
        $objectURL = '/publication/reading/'.$event->article->id.'/'.$event->article->title;
        $fromName = '著作：'.$event->article->title;
        $fromURL = '/publication/cooperation/'.$event->article->id.'/'.$event->article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd(auth('api')->user()->id,auth('api')->user()->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $articleBehavior = '著作摘要已经修改';
        ArticleDynamic::dynamicAdd($event->article->id,$event->article->title,$articleBehavior,$objectName,$objectURL,$createtime);
        // 添加协作事件
        ArticleCooperationEvent::cooperationEventAdd($cooperation->id,auth('api')->user()->id,auth('api')->user()->username,'修改了著作关键词。');

    }
}
