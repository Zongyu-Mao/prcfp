<?php

namespace App\Listeners\Examination\ExamResort;

use App\Events\Examination\ExamResort\ExamResortSupportRejectedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Examination\ExamResort\ExamResortSupportRejectedNotification;
use App\Home\Examination\ExamResort;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamResort\ExamResortEvent;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamResortSupportRejectedListener
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
     * @param  ExamResortSupportRejectedEvent  $event
     * @return void
     */
    public function handle(ExamResortSupportRejectedEvent $event)
    {
        //对帮助的拒绝，只需要通知帮助者即可
        $resort = $event->examResort;
        $exam = Exam::find($resort->exam_id);
        $parentResort = ExamResort::find($resort->pid);
        $createtime = Carbon::now();
        //帮助被拒绝，安慰积分和成长值+10
        User::expAndGrowValue($resort->author_id,'10','10');
        ExamResortEvent::resortEventAdd($resort->exam_id,$parentResort->author_id,$parentResort->author,'拒绝了对<'.$parentResort->title.'>的帮助内容：<'.$resort->title.'>。');
        // 添加到用户动态
        $behavior = '拒绝了帮助：';
        $objectName = $resort->title;
        $objectURL = '/examination/examResort/'.$exam->id.'/'.$exam->title.'#resort'.$resort->id;
        $fromName = '试卷求助：'.$parentResort->title;
        $fromURL = '/examination/examResort/'.$exam->id.'/'.'#resort'.$parentResort->id;
        UserDynamic::dynamicAdd($parentResort->author_id,$parentResort->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加热度记录
        $b_id = 43;
        ExamTemperatureRecord::recordAdd($exam->id,$resort->author_id,$b_id,$createtime);
        // 通知求助者
        User::find($resort->author_id)->notify(new ExamResortSupportRejectedNotification($resort));
    }
}
