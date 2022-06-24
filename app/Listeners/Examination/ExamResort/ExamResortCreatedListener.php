<?php

namespace App\Listeners\Examination\ExamResort;

use App\Events\Examination\ExamResort\ExamResortCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Examination\ExamResort\ExamResortCreatedNotification;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamResort\ExamResortEvent;
use App\Home\Examination\Exam\ExamDynamic;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\Classification;
use App\Home\UserDynamic;
use App\Home\Announcement;
use Carbon\Carbon;
use App\Models\User;

class ExamResortCreatedListener
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
     * @param  ExamResortCreatedEvent  $event
     * @return void
     */
    public function handle(ExamResortCreatedEvent $event)
    {
        //词条创建成功后，写入求助事件，写入公告，通知兴趣用户，求助不会通知协作小组（因为协作小组已经默认关注了词条了），但是求助通知所有与本词条有关的用户
        $resort = $event->examResort;
        $exam = Exam::find($resort->exam_id);
        $manage_id = $exam->manage_id;
        //添加事件到求助事件表
        ExamResortEvent::resortEventAdd($resort->exam_id,$resort->author_id,$resort->author,'发布了求助内容:《'.$resort->title.'》。');
        //发表了有效的讨论后，积分和成长值+20
        User::expAndGrowValue($resort->author_id,'20','20');
        // 添加事件到用户动态
        $behavior = '发布了求助内容：';
        $objectName = $resort->title;
        $objectURL = '/examination/resort/'.$exam->id.'/'.$exam->title.'#resort'.$resort->id;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/resort/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($resort->author_id,$resort->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到著作动态
        $examBehavior = '新增了求助内容';
        ExamDynamic::dynamicAdd($exam->id,$exam->title,$examBehavior,$objectName,$objectURL,$createtime);
        // 添加热度记录
        $b_id = 40;
        ExamTemperatureRecord::recordAdd($exam->id,$resort->author_id,$b_id,$createtime);
        // 发布公告，1代表百科2代表著作，4代表求助
        Announcement::announcementAdd('3','4','试卷《'.$exam->title.'》新增求助内容<'.$resort->title.'>。','/examination/resort/'.$exam->id.'/'.$exam->title.'#'.$resort->id,$resort->created_at);
        // 发布通知
        // 获取词条母专业兴趣人员
        $interestUsers = Classification::where('id',$exam->cid)->first()->getInterestUsers()->pluck('user_id')->toArray();
        // 获取协作成员
        $manage_id = $exam->manage_id;
        array_push($interestUsers, $manage_id);
        // 获取词条的关注用户
        $focusUsers = $exam->ExamFocus()->pluck('user_id')->toArray();
        // 合并所有通知组用户
        $allUsers = array_unique(array_merge($focusUsers,$interestUsers));
        $usersToNotification = User::whereIn('id',$allUsers)->get();
        // 发送通知
        Notification::send($usersToNotification, new ExamResortCreatedNotification($resort));
    }
}
