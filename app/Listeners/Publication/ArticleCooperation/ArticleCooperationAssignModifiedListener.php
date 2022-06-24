<?php

namespace App\Listeners\Publication\ArticleCooperation;

use App\Events\Publication\ArticleCooperation\ArticleCooperationAssignModifiedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Publication\ArticleCooperation\ArticleCooperationEvent;
use Illuminate\Support\Facades\Auth;

class ArticleCooperationAssignModifiedListener
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
     * @param  ArticleCooperationAssignModifiedEvent  $event
     * @return void
     */
    public function handle(ArticleCooperationAssignModifiedEvent $event)
    {
        //assign变化仅需要记录在事件中
        // 添加协作事件
        $user = auth('api')->user();
        ArticleCooperationEvent::cooperationEventAdd($event->articleCooperation->id,$user->id,$user->username,'编辑了新的协作任务。');

    }
}
