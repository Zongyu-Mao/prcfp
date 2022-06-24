<?php

namespace App\Listeners\Examination;

use App\Events\Examination\ExamManagerUpdatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Home\Examination\Exam\ExamDynamic;
use App\Home\Announcement;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamManagerUpdatedListener
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
     * @param  ExamManagerUpdatedEvent  $event
     * @return void
     */
    public function handle(ExamManagerUpdatedEvent $event)
    {
        $c = $event->exam;
        $user = $c->managerInfo;
        $c_url = '/examination/reading/'.$c->id.'/'.$c->title;
        Announcement::announcementAdd(3,6,'试卷['.$c->title.']已经变更新的自管理员['.$user->username.']。',$c_url,$c->updated_at);
        // 添加事件到用户动态
        $behavior = '接管自管理试卷：';
        $objectName = $c->title;
        $objectURL = $c_url;
        $fromName = '试卷：'.$c->title;
        $fromURL = $c_url;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($user->id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        $d_ehavior = '变更自管理员';
        ExamDynamic::dynamicAdd($c->id,$c->title,$d_ehavior,$objectName,$objectURL,$createtime);
    }
}
