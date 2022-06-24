<?php

namespace App\Listeners\Encyclopedia\Entry;

use App\Events\Encyclopedia\Entry\EntryContentDeletedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Encyclopedia\Entry\EntryContent;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationEvent;
use App\Home\Encyclopedia\EntryDynamic;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EntryContentDeletedListener
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
     * @param  EntryContentDeletedEvent  $event
     * @return void
     */
    public function handle(EntryContentDeletedEvent $event)
    {
        // 内容的修改不会通知协作组，只在协作计划事件中写入
        $content = $event->entryContent;
        $cooperation = EntryCooperation::where([['eid',$content->eid],['status','0']])->first();
        $entry = Entry::find($content->eid);
        $entry->increment('edit_number');
        $user_id = auth('api')->user()->id;
        $username = auth('api')->user()->username;
        // 添加事件到用户动态
        $behavior = '删除了词条《'.$entry->title.'》正文原第'.$content->sort.'部分内容：';
        $objectName = $entry->title;
        $objectURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        $fromName = '词条：'.$entry->title;
        $fromURL = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($user_id,$username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到著作动态
        $entryBehavior = '正文内容原第'.$content->sort.'部分已经删除：';
        EntryDynamic::dynamicAdd($entry->id,$entry->title,$entryBehavior,$objectName,$objectURL,$createtime);
        // 添加协作事件
        EntryCooperationEvent::cooperationEventAdd($cooperation->id,$user_id,$username,'删除了原第'.$content->sort.'部分内容。');
    }
}
