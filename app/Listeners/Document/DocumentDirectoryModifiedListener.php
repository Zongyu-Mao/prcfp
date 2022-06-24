<?php

namespace App\Listeners\Document;

use App\Events\Document\DocumentDirectoryModifiedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Document\DocumentDirectory;
use App\Models\User;
use Carbon\Carbon;
use App\Home\UserDynamic;
use App\Home\Announcement;

class DocumentDirectoryModifiedListener
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
     * @param  DocumentDirectoryModifiedEvent  $event
     * @return void
     */
    public function handle(DocumentDirectoryModifiedEvent $event)
    {
        // 添加事件到用户动态1
        $dir = $event->documentDirectory;
        $a = ($dir->created_at==$dir->updated_at);
        $behavior = ($a?'创建':'修改').'了手册文档目录：《'.$dir->classname.'》。';
        $objectName = $dir->classname;
        $objectURL = '/document/directory';
        $fromName = '文档';
        $fromURL = '/document/directory';
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($dir->creator_id,User::find($a?$dir->creator_id:$dir->revisor_id)->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 发布公告5代表创建
        if($a)Announcement::announcementAdd(7,5,'手册文档目录<'.$dir->title.'>已经创建。',$objectURL,$createtime);
    }
}
