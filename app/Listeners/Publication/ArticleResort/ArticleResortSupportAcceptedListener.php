<?php

namespace App\Listeners\Publication\ArticleResort;

use App\Events\Publication\ArticleResort\ArticleResortSupportAcceptedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Publication\ArticleResort\ArticleResortSupportAcceptedNotification;
use App\Notifications\Publication\ArticleResort\ArticleResortSupportUselessNotification;
use App\Notifications\Publication\ArticleResort\ArticleResortSupportAcceptedToUserNotification;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleResort;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\ArticleResort\ArticleResortEvent;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleResortSupportAcceptedListener
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
     * @param  ArticleResortSupportAcceptedEvent  $event
     * @return void
     */
    public function handle(ArticleResortSupportAcceptedEvent $event)
    {
        //帮助被采纳，通知帮助者和小组成员
        $resort = $event->articleResort;
        $article = Article::find($resort->aid);
        $parentResort = ArticleResort::find($resort->pid);
        $createtime = Carbon::now();
        $cooperation = ArticleCooperation::find($article->cooperation_id);
        // 帮助被采纳后，求助更改状态为已解决，帮助更改状态为已采纳，其余帮助均更改为失效。
        ArticleResort::where('id',$resort->pid)->update(['status'=>'1']);
        $elseSupports = ArticleResort::where([['pid',$resort->pid],['status','0']])->get();
        if($elseSupports){
            foreach($elseSupports as $value){
                ArticleResort::where('id',$value->id)->update(['status'=>'3']);
                User::find($value->author_id)->notify(new ArticleResortSupportUselessNotification($resort));
            }
        }
        //接受了帮助后，操作者积分和成长值+10
        User::expAndGrowValue($resort->author_id,'10','10');
        // 添加求助事件
        ArticleResortEvent::resortEventAdd($resort->aid,$parentResort->author_id,$parentResort->author,'接受了'.$resort->author.'的帮助内容:<'.$resort->title.'>。');
        // 添加到用户动态
        $behavior = '采纳了帮助：';
        $objectName = $resort->title;
        $objectURL = '/publication/resort/'.$article->id.'/'.$article->title.'#resort'.$resort->id;
        $fromName = '著作求助：'.$parentResort->title;
        $fromURL = '/publication/resort/'.$article->id.'/'.'#resort'.$parentResort->id;
        UserDynamic::dynamicAdd($parentResort->author_id,$parentResort->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 通知原反对作者被接受
        User::find($resort->author_id)->notify(new ArticleResortSupportAcceptedToUserNotification($resort));
        // 添加热度记录
        $b_id = 42;
        ArticleTemperatureRecord::recordAdd($resort->aid,$resort->author_id,$b_id,$createtime);
        // 开启对协作组成员的通知
        $manage_id = $article->manage_id;
        if(count($cooperation)){
            $crewArr = $cooperation->crews()->pluck('user_id')->toArray();
            $initiate_id = $cooperation->manage_id;
            array_push($crewArr, $manage_id);
            array_push($crewArr, $initiate_id); 
        }else{
            $crewArr = [];
            array_push($crewArr, $manage_id);
        }
        // 获取词条的关注用户
        // $focusUsers = $article->articleFocus()->pluck('user_id')->toArray();
        // 合并协作组
        $users = array_unique($crewArr);
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyArticleAdd($result));
        Notification::send($usersToNotification, new ArticleResortSupportAcceptedNotification($resort));
    }
}
