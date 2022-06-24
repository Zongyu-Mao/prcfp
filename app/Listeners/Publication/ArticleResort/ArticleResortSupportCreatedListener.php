<?php

namespace App\Listeners\Publication\ArticleResort;

use App\Events\Publication\ArticleResort\ArticleResortSupportCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Publication\ArticleResort\ArticleResortSupportCreatedNotification;
use App\Home\Publication\ArticleResort;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleResort\ArticleResortEvent;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleResortSupportCreatedListener
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
     * @param  ArticleResortSupportCreatedEvent  $event
     * @return void
     */
    public function handle(ArticleResortSupportCreatedEvent $event)
    {
        //原则上来说，对求助话题的帮助，只需要通知求助者即可
        $resort = $event->articleResort;
        $article = Article::find($resort->aid);
        $parentResort = ArticleResort::find($resort->pid);
        $createtime = Carbon::now();
        //积分和成长值+50
        User::expAndGrowValue($resort->author_id,'50','50');
        ArticleResortEvent::resortEventAdd($resort->aid,$resort->author_id,$resort->author,'发布了对<'.$parentResort->title.'>的帮助内容：<'.$resort->title.'>。');
        // 添加到用户动态
        $behavior = '发布了帮助：';
        $objectName = $resort->title;
        $objectURL = '/publication/resort/'.$article->id.'/'.$article->title.'#resort'.$resort->id;
        $fromName = '著作求助：'.$parentResort->title;
        $fromURL = '/publication/resort/'.$article->id.'/'.'#resort'.$parentResort->id;
        UserDynamic::dynamicAdd($resort->author_id,$resort->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加热度记录
        $b_id = 41;
        ArticleTemperatureRecord::recordAdd($resort->aid,$resort->author_id,$b_id,$createtime);
        // 通知求助者
        User::find($parentResort->author_id)->notify(new ArticleResortSupportCreatedNotification($resort));
    }
}
