<?php

namespace App\Listeners\Management\Surveillance;

use App\Events\Management\Surveillance\WarningExamEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Management\Surveillance\ExamWarned;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\Exam\ExamDynamic;
use Carbon\Carbon;
use App\Models\User;

class WarningExamListener
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
     * @param  WarningExamEvent  $event
     * @return void
     */
    public function handle(WarningExamEvent $event)
    {
        $warn = $event->surveillanceExamWarning;
        // 获取关注用户
        $exam = $warn->content;
        $objectName = $exam->title;
        $status = $warn->status;
        if($status==0) {
            $behavior = '主内容已被警示。';
        }elseif($status==1) {
            $behavior = '主内容申请警示撤销。';
        }if($status==2) {
            $behavior ='主内容警示已撤销。';
        }
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
        // warn 要通知协作组
        $usersToNotification = User::whereIn('id',$crewArr)->get();
        Notification::send($usersToNotification, new ExamWarned($warn));
    }
}
