<?php

namespace App\Listeners\Examination;

use App\Events\Examination\ExamSummaryModifiedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Examination\ExamCooperation\ExamCooperationEvent;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\Exam\ExamDynamic;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ExamSummaryModifiedListener
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
     * @param  ExamSummaryModifiedEvent  $event
     * @return void
     */
    public function handle(ExamSummaryModifiedEvent $event)
    {
        // 添加事件到用户动态、试卷动态和协作事件
        $exam = $event->exam;
        $cooperation = ExamCooperation::find($exam->cooperation_id);
        $behavior = '编辑了试卷摘要：';
        $objectName = $exam->title;
        $objectURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/cooperation/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        $user = auth('api')->user();
        UserDynamic::dynamicAdd($user->id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $examBehavior = '试卷摘要已经修改';
        ExamDynamic::dynamicAdd($exam->id,$exam->title,$examBehavior,$objectName,$objectURL,$createtime);
        // 添加协作事件
        ExamCooperationEvent::cooperationEventAdd($cooperation->id,$user->id,$user->username,'修改了试卷摘要。');
    }
}
