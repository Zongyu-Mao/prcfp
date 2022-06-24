<?php

namespace App\Listeners\Publication\Article\ArticleReference;

use App\Events\Publication\Article\ArticleReference\ArticleReferenceModifiedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Publication\Article;
use App\Home\Publication\Article\ArticleContent;
use App\Models\Publication\Article\ArticlePart;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\ArticleCooperation\ArticleCooperationEvent;
use App\Home\Publication\Article\ArticleDynamic;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleReferenceModifiedListener
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
     * @param  ArticleReferenceModifiedEvent  $event
     * @return void
     */
    public function handle(ArticleReferenceModifiedEvent $event)
    {
        //参考文献添加后，添加该事件到协作动态，暂时不通知协作组成员和词条关注者参考文献新增了
        $ref = $event->articleReference;
        $part = ArticleContent::find($ref->part_id);
        $article = Article::find($part->aid);
        $article->increment('edit_number');
        $cooperation = ArticleCooperation::find($article->cooperation_id);
        $user = User::find($ref->creator);
        // 添加事件到用户动态
        $behavior = '修改了参考文献：';
        $objectName = $ref->title;
        $objectURL = '/publication/reading/'.$article->id.'/'.$article->title.'#reference'.$ref->id;
        $fromName = '著作：《'.$article->title.'》第'.$part->sort.'分部：<'.$part->title.'>';
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($user->id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $articleBehavior = '第'.$part->sort.'分部：<'.$part->title.'>参考文献['.$ref->sort.']已经修改';
        ArticleDynamic::dynamicAdd($article->id,$article->title,$articleBehavior,$objectName,$objectURL,$createtime);
        // 添加协作事件
        ArticleCooperationEvent::cooperationEventAdd($cooperation->id,$user->id,$user->username,'添加了参考文献['.$ref->sort.']<'.$ref->title.'>。');
    }
}
