<?php

namespace App\Listeners\Management\Surveillance;

use App\Events\Management\Surveillance\WarningArticleEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Management\Surveillance\ArticleWarned;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\Article\ArticleDynamic;
use Carbon\Carbon;
use App\Models\User;

class WarningArticleListener
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
     * @param  WarningArticleEvent  $event
     * @return void
     */
    public function handle(WarningArticleEvent $event)
    {
        $warn = $event->surveillanceArticleWarning;
        // 获取关注用户
        $article = $warn->content;
        $objectName = $article->title;
        $status = $warn->status;
        if($status==0) {
            $behavior = '主内容已被警示。';
        }elseif($status==1) {
            $behavior = '主内容申请警示撤销。';
        }if($status==2) {
            $behavior ='主内容警示已撤销。';
        }
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
        // warning 要通知协作组
        $usersToNotification = User::whereIn('id',$crewArr)->get();
        Notification::send($usersToNotification, new ArticleWarned($warn));
    }
}
