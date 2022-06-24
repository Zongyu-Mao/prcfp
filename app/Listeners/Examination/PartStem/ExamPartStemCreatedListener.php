<?php

namespace App\Listeners\Examination\PartStem;

use App\Events\Examination\PartStem\ExamPartStemCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Examination\Exam\ExamContent;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\ExamCooperation\ExamCooperationEvent;
use App\Home\Examination\Exam\ExamDynamic;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamPartStemCreatedListener
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
     * @param  ExamPartStemCreatedEvent  $event
     * @return void
     */
    public function handle(ExamPartStemCreatedEvent $event)
    {
        //材料创建后，添加到用户动态，写入协作计划事件，写入试卷动态
        $stem = $event->examPartStem;
        $cooperation_id = ExamCooperation::where([['exam_id',$stem->exam_id],['status','0']])->first()->id;
        $exam = Exam::find($stem->exam_id);
        $editorUser = User::find($stem->creator_id);
        // 添加事件到用户动态
        $behavior = '添加了试卷材料（'.$stem->sort.'）';
        // 没有为材料设定名称，因此要么截取，要么直接用字符串代替了
        $objectName = substr($stem->content,0,30).'...';
        $objectURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($editorUser->id,$editorUser->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到著作动态
        $ExamBehavior = '试卷材料（'.$stem->sort.'）已经添加：';
        ExamDynamic::dynamicAdd($exam->id,$exam->title,$ExamBehavior,$objectName,$objectURL,$createtime);
        // 添加到协作事件
        ExamCooperationEvent::cooperationEventAdd($cooperation_id,$editorUser->id,$editorUser->username,$behavior);
    }
}
