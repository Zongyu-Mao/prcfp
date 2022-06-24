<?php

namespace App\Listeners\Examination\ExamCooperation\Member;

use App\Events\Examination\ExamCooperation\Member\MemberQuittedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\ExamCooperation\ExamCooperationEvent;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\Examination\Exam;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Home\Cooperation\ExamContributeValue;

class MemberQuittedListener
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
     * @param  MemberQuittedEvent  $event
     * @return void
     */
    public function handle(MemberQuittedEvent $event)
    {
        // 组员退出后，写入事件，并发送通知给被请出组员
        $cooperationUser = $event->examCooperationUser;
        $cooperation = ExamCooperation::find($cooperationUser->cooperation_id);
        $exam = Exam::find($cooperation->exam_id);
        $crew = User::find($cooperationUser->user_id);
        ExamContributeValue::contributeDelete($cooperationUser->cooperation_id,$cooperationUser->user_id);
        ExamCooperationEvent::cooperationEventAdd($cooperation->id,$crew->id,$crew->username,'退出协作计划。');
        // 写入用户动态
        $behavior = '退出试卷协作计划：';
        $objectName = $cooperation->title;
        $objectURL = '/examination/cooperation/'.$exam->id.'/'.$exam->title;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($crew->id,$crew->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 试卷添加热度记录
        $b_id = 17;
        ExamTemperatureRecord::recordAdd($exam->id,$crew->id,$b_id,$createtime);
    }
}
