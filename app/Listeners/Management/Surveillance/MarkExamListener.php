<?php

namespace App\Listeners\Management\Surveillance;

use App\Events\Management\Surveillance\MarkExamEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Management\Surveillance\ExamMarked;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\Exam\ExamDynamic;
use Carbon\Carbon;
use App\Models\User;

class MarkExamListener
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
     * @param  MarkExamEvent  $event
     * @return void
     */
    public function handle(MarkExamEvent $event)
    {
        // 标记发生后
        $mark = $event->surveillanceExamMark;
        // 获取关注用户
        $exam = $mark->content;
        $objectName = $exam->title;
        $behavior = '主内容已被标记';
        $objectURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        // 写入动态
        ExamDynamic::dynamicAdd($exam->id,$exam->title,$behavior,$objectName,$objectURL,$createtime);
        $cooperation = ExamCooperation::find($exam->cooperation_id);
        $crewArr = [];
        if($cooperation){
            $crewArr = $cooperation->crews()->pluck('user_id')->toArray();
        } 
        array_push($crewArr, $exam->manage_id);
        // mark 要通知协作组
        $usersToNotification = User::whereIn('id',$crewArr)->get();
        Notification::send($usersToNotification, new ExamMarked($mark));
    }
}
