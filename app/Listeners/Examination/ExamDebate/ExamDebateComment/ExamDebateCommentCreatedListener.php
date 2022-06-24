<?php

namespace App\Listeners\Examination\ExamDebate\ExamDebateComment;

use App\Events\Examination\ExamDebate\ExamDebateComment\ExamDebateCommentCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Examination\ExamDebate\ExamDebateComment\ExamDebateCommentRepliedNotification;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamDebate\ExamDebateComment;
use App\Home\Examination\ExamDebate\ExamDebateEvent;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use Carbon\Carbon;
use App\Models\User;

class ExamDebateCommentCreatedListener
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
     * @param  ExamDebateCommentCreatedEvent  $event
     * @return void
     */
    public function handle(ExamDebateCommentCreatedEvent $event)
    {
        //
        $comment = $event->examDebateComment;
        // $exam = Exam::find($comment->exam_id);
        $createtime = Carbon::now();
        //添加事件到辩论事件表
        ExamDebateEvent::debateEventAdd($comment->debate_id,$comment->author_id,$comment->getAuthor->username,'发表了新的评论:<'.$comment->title.'>。');
        // 词条添加热度记录
        if($comment->pid == 0){
            $b_id = 69;
        }else{
            $b_id = 70;
            User::find(ExamDebateComment::find($comment->pid)->author_id)->notify(new ExamDebateCommentRepliedNotification($comment));
        }
        ExamTemperatureRecord::recordAdd($comment->exam_id,$comment->author_id,$b_id,$createtime);
    }
}
