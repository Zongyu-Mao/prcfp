<?php

namespace App\Listeners\Examination;

use App\Events\Examination\ExamCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Examination\Exam\ExamCreatedNotification;
use App\Notifications\Examination\Exam\InterestSpecialtyExamCreatedNotification;
use App\Home\Examination\ExamCooperation\ExamCooperationEvent;
use App\Home\Examination\Exam\ExamDynamic;
use App\Home\Examination\ExamCooperation;
use App\Home\Announcement;
use App\Home\Classification;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamCreatedListener
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
     * @param  ExamCreatedEvent  $event
     * @return void
     */
    public function handle(ExamCreatedEvent $event)
    {
        //著作成功创建后
        // 3代表试卷，5代表创建（此时不考虑协作计划创建公告，因为与词条创建是同步的）
        $exam = $event->exam;
        Announcement::announcementAdd('3','5','试卷['.$exam->title.']已经创建。','/examination/reading/'.$exam->id.'/'.$exam->title,$exam->created_at);
        // 添加事件到用户动态
        $behavior = '创建了试卷：';
        $objectName = $exam->title;
        $objectURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        $user = User::find($exam->creator_id);
        UserDynamic::dynamicAdd($exam->creator_id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到著作动态
        $examBehavior = '试卷已经创建：';
        ExamDynamic::dynamicAdd($exam->id,$exam->title,$examBehavior,$objectName,$objectURL,$createtime);
        // 通知创建者创建成功
        $user->notify(new ExamCreatedNotification($exam));
        // 通知该专业兴趣人员新增了新的著作
        $users = Classification::where('id',$exam->cid)->first()->getInterestUsers()->pluck('user_id')->toArray();
        // if(in_array($creator_id, $users)){
        //     array_forget($users,$creator_id);
        // }
        $users = array_unique($users);
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyEntryAdd($result));
        Notification::send($usersToNotification, new InterestSpecialtyExamCreatedNotification($exam));
    }
}
