<?php

namespace App\Listeners\Examination\ExamReview;

use App\Events\Examination\ExamReview\ExamReviewAdvisementAcceptedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Examination\ExamReview\ExamReviewAdvisementAcceptedNotification;
use App\Home\Examination\ExamReview;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamReview\ExamReviewEvent;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamReviewAdvisementAcceptedListener
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
     * @param  ExamReviewAdvisementAcceptedEvent  $event
     * @return void
     */
    public function handle(ExamReviewAdvisementAcceptedEvent $event)
    {
        //评审计划建议被小组接受后，通知原作者
        $advise = $event->examReviewAdvise;
        $review = ExamReview::find($advise->rid);
        $exam = Exam::find($review->exam_id);
        // 添加事件到用户动态
        $behavior = '接受评审计划建议：';
        $objectName = $advise->title;
        $objectURL = '/examination/review/'.$exam->id.'/'.$exam->title.'#reviewAdvise'.$advise->id;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($advise->recipient_id,$advise->recipient,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        //积分和成长值+10
        User::expAndGrowValue($advise->recipient_id,'10','10');
        ExamReviewEvent::reviewEventAdd($advise->rid,$advise->recipient_id,$advise->recipient,'接受了'.$advise->author.'的建议评论。');
        // 添加热度记录
        $b_id = 31;
        ExamTemperatureRecord::recordAdd($exam->id,$advise->recipient_id,$b_id,$createtime);
        // 通知被回复作者
        User::find($advise->author_id)->notify(new ExamReviewAdvisementAcceptedNotification($advise));
    }
}
