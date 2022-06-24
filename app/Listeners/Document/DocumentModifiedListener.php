<?php

namespace App\Listeners\Document;

use App\Events\Document\DocumentModifiedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Document\Document;
use App\Models\User;
use Carbon\Carbon;
use App\Home\UserDynamic;

class DocumentModifiedListener
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
     * @param  DocumentModifiedEvent  $event
     * @return void
     */
    public function handle(DocumentModifiedEvent $event)
    {
        // 添加事件到用户动态
        $doc = $event->document;
        $behavior = '更改了手册文档：《'.$doc->title.'》。';
        $objectName = $doc->title;
        $objectURL = '/document/reading/'.$doc->id.'/'.$doc->title;
        $fromName = '文档';
        $fromURL = '/document';
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($doc->creator_id,User::find($doc->creator_id)->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
    }
}
