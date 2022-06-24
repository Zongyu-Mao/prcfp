<?php

namespace App\Listeners\Publication\ArticleResort;

use App\Events\Publication\ArticleResort\ArticleResortSupportRejectedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Publication\ArticleResort\ArticleResortSupportRejectedNotification;
use App\Home\Publication\ArticleResort;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleResort\ArticleResortEvent;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleResortSupportRejectedListener
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
     * @param  ArticleResortSupportRejectedEvent  $event
     * @return void
     */
    public function handle(ArticleResortSupportRejectedEvent $event)
    {
        //对帮助的拒绝，只需要通知帮助者即可
        $resort = $event->articleResort;
        $article = Article::find($resort->aid);
        $parentResort = ArticleResort::find($resort->pid);
        $createtime = Carbon::now();
        //帮助被拒绝，安慰积分和成长值+10
        User::expAndGrowValue($resort->author_id,'10','10');
        ArticleResortEvent::resortEventAdd($resort->aid,$parentResort->author_id,$parentResort->author,'拒绝了对<'.$parentResort->title.'>的帮助内容：<'.$resort->title.'>。');
        // 添加到用户动态
        $behavior = '拒绝了帮助：';
        $objectName = $resort->title;
        $objectURL = '/publication/resort/'.$article->id.'/'.$article->title.'#resort'.$resort->id;
        $fromName = '著作求助：'.$parentResort->title;
        $fromURL = '/publication/resort/'.$article->id.'/'.'#resort'.$parentResort->id;
        UserDynamic::dynamicAdd($parentResort->author_id,$parentResort->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加热度记录
        $b_id = 43;
        ArticleTemperatureRecord::recordAdd($resort->aid,$resort->author_id,$b_id,$createtime);
        // 通知求助者
        User::find($resort->author_id)->notify(new ArticleResortSupportRejectedNotification($resort));
    }
}
