<?php

namespace App\Listeners\Publication\Article\ArticleExtendedReading;

use App\Events\Publication\Article\ArticleExtendedReading\ArticleExtendedArticleReadingDeletedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Publication\Article\ExtendedReading\ArticleExtendedArticleReading;
use App\Home\Publication\ArticleCooperation\ArticleCooperationEvent;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\Article;
use App\Home\Publication\Article\ArticleDynamic;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ArticleExtendedArticleReadingDeletedListener
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
     * @param  ArticleExtendedArticleReadingDeletedEvent  $event
     * @return void
     */
    public function handle(ArticleExtendedArticleReadingDeletedEvent $event)
    {
        // 得到延伸著作、被延伸内容,写入用户动态和协作事件，不需要通知
        $article = Article::find($event->articleExtendedArticleReading->aid);
        $extended = Article::find($event->articleExtendedArticleReading->extended_id);
        $cooperation = ArticleCooperation::find($article->cooperation_id);
        $user = auth('api')->user();
        // 添加协作事件
        ArticleCooperationEvent::cooperationEventAdd($cooperation->id,$user->id,$user->username,'删除了原著作延伸阅读内容《'.$extended->title.'》。');
        // 添加事件到用户动态
        $behavior = '删除了原著作延伸阅读内容：';
        $objectName = $extended->title;
        $objectURL = '/publication/reading/'.$extended->id.'/'.$extended->title;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($user->id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到著作动态
        $ArticleBehavior = '删除了原著作延伸阅读内容：';
        ArticleDynamic::dynamicAdd($article->id,$article->title,$ArticleBehavior,$objectName,$objectURL,$createtime);
        // 添加事件到被延伸著作动态
        $extendBehavior = '已经被删除延伸阅读关系。';
        ArticleDynamic::dynamicAdd($extended->id,$extended->title,$extendBehavior,$fromName,$fromURL,$createtime);
        // 主动词条添加热度记录
        $b_id = 11;
        ArticleTemperatureRecord::recordAdd($article->id,$user->id,$b_id,$createtime);
        // 被延伸著作添加热度记录
        $be_id = 13;
        ArticleTemperatureRecord::recordAdd($extended->id,$user->id,$be_id,$createtime);
    }
}
