<?php

namespace App\Listeners\Examination\ExamDiscussion;

use App\Events\Examination\ExamDiscussion\ExamDiscussionRepliedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Examination\ExamDiscussion\ExamDiscussionRepliedNotification;
use App\Home\Examination\ExamDiscussion;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamDiscussion\ExamDiscussionEvent;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamDiscussionRepliedListener
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
     * @param  ExamDiscussionRepliedEvent  $event
     * @return void
     */
    public function handle(ExamDiscussionRepliedEvent $event)
    {
        // 普通讨论被回复后，仅通知讨论的作者
        $discussion = $event->examDiscussion;
        $exam = Exam::find($discussion->exam_id);
        $parentDiscussion = ExamDiscussion::find($discussion->pid);
        // 添加事件到用户动态
        $behavior = '试卷著作普通讨论：';
        $objectName = $discussion->title;
        $objectURL = '/examination/discussion/'.$exam->id.'/'.$exam->title.'#discussion'.$discussion->id;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($discussion->author_id,$discussion->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        //讨论被回复后，回复者的积分和成长值+10
        User::expAndGrowValue($discussion->author_id,'10','10');
        // 添加讨论事件
        ExamDiscussionEvent::discussionEventAdd($exam->id,$discussion->author_id,$discussion->author,'回复了['.$parentDiscussion->author.']提出的讨论内容<'.$parentDiscussion->title.'>。');
        // 添加热度记录
        $b_id = 55;
        ExamTemperatureRecord::recordAdd($exam->id,$discussion->author_id,$b_id,$createtime);
        // 给反对作者发送通知
        User::find($parentDiscussion->author_id)->notify(new ExamDiscussionRepliedNotification($discussion));
    }
}
