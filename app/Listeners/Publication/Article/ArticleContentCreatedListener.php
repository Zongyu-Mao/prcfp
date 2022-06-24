<?php

namespace App\Listeners\Publication\Article;

use App\Events\Publication\Article\ArticleContentCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Publication\Article\ArticleContent;
use App\Home\Publication\Article;
use Illuminate\Support\Facades\Notification;
use App\Home\Publication\ArticleCooperation;
use App\Notifications\Publication\Article\ArticleContentCreatedNotification;
use App\Home\Publication\Article\ArticleDynamic;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;


class ArticleContentCreatedListener
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
     * @param  ArticleContentCreatedEvent  $event
     * @return void
     */
    public function handle(ArticleContentCreatedEvent $event)
    {
        //内容创建后，添加到用户动态，写入协作计划事件，写入著作动态，如果有协作组的，应该通知协作组，如果有关注和收藏的，应该通知关注
        $cooperation = ArticleCooperation::where([['aid',$event->articleContent->aid],['status','0']])->first();
        $article = Article::find($event->articleContent->aid);
        $article->increment('edit_number');
        $editorUser = User::find($event->articleContent->editor_id);
        // 添加事件到用户动态
        $behavior = '添加了著作正文第'.$event->articleContent->sort.'章节：';
        $objectName = $article->title;
        $objectURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($editorUser->id,$editorUser->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到著作动态
        $articleBehavior = '正文第'.$event->articleContent->sort.'章节已经添加：';
        ArticleDynamic::dynamicAdd($article->id,$article->title,$articleBehavior,$objectName,$objectURL,$createtime);
        
        $manage_id = $article->manage_id;
        if($cooperation){
            $crewArr = $cooperation->crews()->pluck('user_id')->toArray();
            $initiate_id = $cooperation->manage_id;
            array_push($crewArr, $manage_id);
            array_push($crewArr, $initiate_id); 
        }else{
            $crewArr = [];
            array_push($crewArr, $manage_id);
        }
        // 获取词条的关注用户
        $focusUsers = $article->articleFocus()->pluck('user_id')->toArray();
        // 合并协作组与兴趣用户
        $users = array_unique(array_merge($crewArr,$focusUsers));
        $usersToNotification = User::whereIn('id',$users)->get();
        Notification::send($usersToNotification, new ArticleContentCreatedNotification($event->articleContent));
    }
}
