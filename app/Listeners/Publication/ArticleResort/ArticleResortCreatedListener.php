<?php

namespace App\Listeners\Publication\ArticleResort;

use App\Events\Publication\ArticleResort\ArticleResortCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Publication\ArticleResort\ArticleResortCreatedNotification;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleResort\ArticleResortEvent;
use App\Home\Publication\Article\ArticleDynamic;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\Classification;
use App\Home\UserDynamic;
use App\Home\Announcement;
use Carbon\Carbon;
use App\Models\User;

class ArticleResortCreatedListener
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
     * @param  ArticleResortCreatedEvent  $event
     * @return void
     */
    public function handle(ArticleResortCreatedEvent $event)
    {
        //求助创建成功后，写入求助事件，写入公告，通知兴趣用户，求助不会通知协作小组（因为协作小组已经默认关注了词条了），但是求助通知所有与本词条有关的用户
        $resort = $event->articleResort;
        $article = Article::find($resort->aid);
        $manage_id = $article->manage_id;
        //添加事件到求助事件表
        ArticleResortEvent::resortEventAdd($resort->aid,$resort->author_id,$resort->author,'发布了求助内容:《'.$resort->title.'》。');
        //发表了有效的讨论后，积分和成长值+20
        User::expAndGrowValue($resort->author_id,'20','20');
        // 添加事件到用户动态
        $behavior = '发布了求助内容：';
        $objectName = $resort->title;
        $objectURL = '/publication/resort/'.$article->id.'/'.$article->title.'#resort'.$resort->id;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/resort/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($resort->author_id,$resort->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加热度记录
        $b_id = 40;
        ArticleTemperatureRecord::recordAdd($resort->aid,$resort->author_id,$b_id,$createtime);
        // 添加事件到著作动态
        $articleBehavior = '新增了求助内容';
        ArticleDynamic::dynamicAdd($article->id,$article->title,$articleBehavior,$objectName,$objectURL,$createtime);
        // 发布公告，1代表百科2代表著作，4代表求助
        Announcement::announcementAdd('2','4','著作《'.$article->title.'》新增求助内容<'.$resort->title.'>。','/publication/resort/'.$article->id.'/'.$article->title.'#resort'.$resort->id,$resort->created_at);
        // 发布通知
        // 获取词条母专业兴趣人员
        $interestUsers = Classification::where('id',$article->cid)->first()->getInterestUsers()->pluck('user_id')->toArray();
        // 获取协作成员
        $manage_id = $article->manage_id;
        array_push($interestUsers, $manage_id);
        // 获取词条的关注用户
        $focusUsers = $article->articleFocus()->pluck('user_id')->toArray();
        // 合并所有通知组用户
        $allUsers = array_unique(array_merge($focusUsers,$interestUsers));
        $usersToNotification = User::whereIn('id',$allUsers)->get();
        // 发送通知
        Notification::send($usersToNotification, new ArticleResortCreatedNotification($resort));
    }
}
