<?php

namespace App\Listeners\Examination\Exam;

use App\Events\Examination\Exam\ExamQuestionCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\ExamCooperation\ExamCooperationEvent;
use App\Home\Examination\Exam\ExamDynamic;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamQuestionCreatedListener
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
     * @param  ExamQuestionCreatedEvent  $event
     * @return void
     */
    public function handle(ExamQuestionCreatedEvent $event)
    {
        //试题创建后，添加到用户动态，写入协作计划事件，写入试卷动态
        $question = $event->examQuestion;
        $cooperation_id = ExamCooperation::where([['exam_id',$question->exam_id],['status','0']])->first()->id;
        $exam = Exam::find($question->exam_id);
        $exam->increment('edit_number');
        $editorUser = User::find($question->creator_id);
        if($question->type == 1){
            $type = '单选题';
        }elseif($question->type == 2){
            $type = '多选题';
        }
        Exam::totalUpdate($question->exam_id,$exam->total+$question->score);
        // 添加事件到用户动态
        $behavior = '添加了试卷第（'.$question->sort.'）题('.$type.')';
        // 没有为材料设定名称，因此要么截取，要么直接用字符串代替了
        $objectName = substr($question->content,0,30).'...';
        $objectURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($editorUser->id,$editorUser->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到著作动态
        $ExamBehavior = '试卷第（'.$question->sort.'）题('.$type.')已经添加：';
        ExamDynamic::dynamicAdd($exam->id,$exam->title,$ExamBehavior,$objectName,$objectURL,$createtime);
        // 添加到协作事件
        ExamCooperationEvent::cooperationEventAdd($cooperation_id,$editorUser->id,$editorUser->username,$behavior);
    }
}
