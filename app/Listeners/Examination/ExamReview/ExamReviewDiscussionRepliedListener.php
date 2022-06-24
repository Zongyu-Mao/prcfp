<?php

namespace App\Listeners\Examination\ExamReview;

use App\Events\Examination\ExamReview\ExamReviewDiscussionRepliedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Examination\ExamReview\ExamReviewDiscussionRepliedNotification;
use App\Home\Examination\ExamReview;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamReview\ExamReviewEvent;
use App\Home\Examination\ExamReview\ExamReviewDiscussion;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamReviewDiscussionRepliedListener
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
     * @param  ExamReviewDiscussionRepliedEvent  $event
     * @return void
     */
    public function handle(ExamReviewDiscussionRepliedEvent $event)
    {
        //评论回复仅需通知被回复评论作者即可
        $discussion = $event->examReviewDiscussion;
        $examReview = ExamReview::find($discussion->rid);
        $exam = Exam::find($examReview->exam_id);
        $parentDiscussion = ExamReviewDiscussion::find($discussion->pid);
        // 添加事件到用户动态
        $behavior = '回复了评审计划支持/中立意见：';
        $objectName = $examReview->title;
        $objectURL = '/examination/review/'.$exam->id.'/'.$exam->title.'#reviewDiscussion'.$discussion->id;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($discussion->author_id,$discussion->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        //发表了有效的评审意见后，积分和成长值+50
        User::expAndGrowValue($discussion->author_id,'10','10');
        ExamReviewEvent::reviewEventAdd($discussion->rid,$discussion->author_id,$discussion->author,'回复了'.$parentDiscussion->getAuthor->username.'的评论。');
        // 添加热度记录
        $b_id = 28;
        ExamTemperatureRecord::recordAdd($exam->id,$discussion->author_id,$b_id,$createtime);
        // 通知被回复作者
        User::find($parentDiscussion->author_id)->notify(new ExamReviewDiscussionRepliedNotification($discussion));
    }
}
