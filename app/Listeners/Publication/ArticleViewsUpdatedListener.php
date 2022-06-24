<?php

namespace App\Listeners\Publication;

use App\Events\Publication\ArticleViewsUpdatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Publication\Recommend\ArticleTemperature;
use App\Home\Personnel\Behavior;

class ArticleViewsUpdatedListener
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
     * @param  ArticleViewsUpdatedEvent  $event
     * @return void
     */
    public function handle(ArticleViewsUpdatedEvent $event)
    {
        //浏览量的增加会引起热度的更新
        $article = $event->article;
        // 添加热度记录
        $t = Behavior::find(3)->score; 
        $ext = ArticleTemperature::where('aid',$article->id)->first();
        $old_tem = $ext->temperature;
        $tem = $old_tem + $t;
        ArticleTemperature::recommendationUpdate($ext->id,$tem);
    }
}
