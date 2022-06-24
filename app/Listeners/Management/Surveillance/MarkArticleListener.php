<?php

namespace App\Listeners\Management\Surveillance;

use App\Events\Management\Surveillance\MarkArticleEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Management\Surveillance\ArticleMarked;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\Article\ArticleDynamic;
use Carbon\Carbon;
use App\Models\User;

class MarkArticleListener
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
     * @param  MarkArticleEvent  $event
     * @return void
     */
    public function handle(MarkArticleEvent $event)
    {
        // 标记发生后
        $mark = $event->surveillanceArticleMark;
        // 获取关注用户
        $article = $mark->content;
        $objectName = $article->title;
        $behavior = '主内容已被标记';
        $objectURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        // 写入动态
        ArticleDynamic::dynamicAdd($article->id,$article->title,$behavior,$objectName,$objectURL,$createtime);
        $cooperation = ArticleCooperation::find($article->cooperation_id);
        $crewArr = [];
        if($cooperation){
            $crewArr = $cooperation->crews()->pluck('user_id')->toArray();
        } 
        array_push($crewArr, $article->manage_id);
        // mark 要通知协作组
        $usersToNotification = User::whereIn('id',$crewArr)->get();
        Notification::send($usersToNotification, new ArticleMarked($mark));
    }
}
