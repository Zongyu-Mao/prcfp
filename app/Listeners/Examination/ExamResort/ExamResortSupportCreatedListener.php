<?php

namespace App\Listeners\Examination\ExamResort;

use App\Events\Examination\ExamResort\ExamResortSupportCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Examination\ExamResort\ExamResortSupportCreatedNotification;
use App\Home\Examination\ExamResort;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamResort\ExamResortEvent;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamResortSupportCreatedListener
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
     * @param  ExamResortSupportCreatedEvent  $event
     * @return void
     */
    public function handle(ExamResortSupportCreatedEvent $event)
    {
        //原则上来说，对求助话题的帮助，只需要通知求助者即可
        $resort = $event->examResort;
        $exam = Exam::find($resort->exam_id);
        $parentResort = ExamResort::find($resort->pid);
        $createtime = Carbon::now();
        //积分和成长值+50
        User::expAndGrowValue($resort->author_id,'50','50');
        ExamResortEvent::resortEventAdd($resort->exam_id,$resort->author_id,$resort->author,'发布了对<'.$parentResort->title.'>的帮助内容：<'.$resort->title.'>。');
        // 添加到用户动态
        $behavior = '发布了帮助：';
        $objectName = $resort->title;
        $objectURL = '/examination/resort/'.$exam->id.'/'.$exam->title.'#resort'.$resort->id;
        $fromName = '试卷求助：'.$parentResort->title;
        $fromURL = '/examination/resort/'.$exam->id.'/'.'#examResort'.$parentResort->id;
        UserDynamic::dynamicAdd($resort->author_id,$resort->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加热度记录
        $b_id = 41;
        ExamTemperatureRecord::recordAdd($exam->id,$resort->author_id,$b_id,$createtime);
        // 通知求助者
        User::find($parentResort->author_id)->notify(new ExamResortSupportCreatedNotification($resort));
    }
}
