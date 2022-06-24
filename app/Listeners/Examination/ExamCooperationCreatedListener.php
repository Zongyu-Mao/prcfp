<?php

namespace App\Listeners\Examination;

use App\Events\Examination\ExamCooperationCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Examination\ExamCooperation\ExamCooperationEvent;
use App\Home\Examination\Exam\ExamDynamic;
use App\Home\Examination\Exam;
use App\Home\Announcement;
use App\Home\UserDynamic;
use Carbon\Carbon;

class ExamCooperationCreatedListener
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
     * @param  ExamCooperationCreatedEvent  $event
     * @return void
     */
    public function handle(ExamCooperationCreatedEvent $event)
    {
        //协作计划创建成功后，写入协作事件，写入公告，写入试卷动态、用户动态
        $cooperation = $event->examCooperation;
        $exam = Exam::find($cooperation->exam_id);
        $manage_id = $exam->manage_id;
        // 添加协作事件
        ExamCooperationEvent::cooperationEventAdd($cooperation->id,$cooperation->creator_id,$cooperation->creator,'创建了协作计划：['.$cooperation->title.']。');
        // 更新词条协作计划id
        // Exam::where('id',$cooperation->aid)->update(['cooperation_id' => $cooperation->id]);
        // 添加事件到用户动态
        $behavior = '开启了协作计划：';
        $objectName = $cooperation->title;
        $objectURL = '/examination/cooperation/'.$exam->id.'/'.$exam->title;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($cooperation->creator_id,$cooperation->creator,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到试卷动态
        $examBehavior = '试卷协作计划'.$objectName.'已经创建：';
        ExamDynamic::dynamicAdd($exam->id,$exam->title,$examBehavior,$objectName,$objectURL,$createtime);
        // 发布公告，1代表百科，2代表试卷，3代表试卷，1代表协作计划
        Announcement::announcementAdd(3,1,'试卷《'.$exam->title.'》的协作计划<'.$cooperation->title.'>已经创建。','/examination/cooperation/'.$exam->id.'/'.$exam->title,$cooperation->created_at);
    }
}
