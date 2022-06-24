<?php

namespace App\Listeners\Examination\ExamCooperation\Member;

use App\Events\Examination\ExamCooperation\Member\MemberJoinedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Examination\ExamCooperation\ExamCooperationEvent;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\Exam\ExamDynamic;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\Examination\Exam;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Home\Cooperation\ExamContributeValue;

class MemberJoinedListener
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
     * @param  MemberJoinedEvent  $event
     * @return void
     */
    public function handle(MemberJoinedEvent $event)
    {
        // 成功写入协作成员后，触发事件：协作事件，著作动态，用户动态；此处暂时不产生通知
        $cooperationUser = $cooperationUser;
        $cooperation = ExamCooperation::find($cooperationUser->cooperation_id);
        $exam = Exam::find($cooperation->exam_id);
        $user = User::find($cooperationUser->user_id);
        $creatime = Carbon::now();
        // 写入贡献表
        ExamContributeValue::contributeAdd($cooperationUser->cooperation_id,$cooperationUser->user_id,0);
        // 写入协作事件
        ExamCooperationEvent::cooperationEventAdd($cooperation->id,$user->id,$user->username,'成功加入协作小组，大家合作愉快。');
        // 添加事件到用户动态
        $behavior = '加入了试卷协作计划：';
        $objectName = $cooperation->title;
        $objectURL = '/examination/cooperation/'.$exam->id.'/'.$Exam->title;
        $fromName = '试卷'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($user->id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $ExamBehavior = '新增协作计划成员：['.$user->username.']';
        ExamDynamic::dynamicAdd($exam->id,$exam->title,$ExamBehavior,$objectName,$objectURL,$createtime);
        // 词条添加热度记录
        $b_id = 16;
        ExamTemperatureRecord::recordAdd($exam->id,$use->id,$b_id,$createtime);
    }
}
