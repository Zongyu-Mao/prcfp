<?php

namespace App\Listeners\Examination\ExamResort;

use App\Events\Examination\ExamResort\ExamResortSupportAcceptedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Examination\ExamResort\ExamResortSupportAcceptedNotification;
use App\Notifications\Examination\ExamResort\ExamResortSupportUselessNotification;
use App\Notifications\Examination\ExamResort\ExamResortSupportAcceptedToUserNotification;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamResort;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\ExamResort\ExamResortEvent;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ExamResortSupportAcceptedListener
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
     * @param  ExamResortSupportAcceptedEvent  $event
     * @return void
     */
    public function handle(ExamResortSupportAcceptedEvent $event)
    {
        //帮助被采纳，通知帮助者和小组成员
        $resort = $event->examResort;
        $exam = Exam::find($resort->exam_id);
        $parentResort = ExamResort::find($resort->pid);
        $cooperation = ExamCooperation::find($exam->cooperation_id);
        $createtime = Carbon::now();
        // 帮助被采纳后，求助更改状态为已解决，帮助更改状态为已采纳，其余帮助均更改为失效。
        ExamResort::where('id',$resort->pid)->update(['status'=>'1']);
        $elseSupports = ExamResort::where([['pid',$resort->pid],['status','0']])->get();
        if(count($elseSupports)){
            foreach($elseSupports as $value){
                ExamResort::where('id',$value->id)->update(['status'=>'3']);
                User::find($value->author_id)->notify(new ExamResortSupportUselessNotification($resort));
            }
        }
        //接受了帮助后，操作者积分和成长值+10
        User::expAndGrowValue($resort->author_id,'10','10');
        // 添加求助事件
        ExamResortEvent::resortEventAdd($resort->exam_id,$parentResort->author_id,$parentResort->author,'接受了'.$resort->author.'的帮助内容:<'.$resort->title.'>。');
        // 添加到用户动态
        $behavior = '采纳了帮助：';
        $objectName = $resort->title;
        $objectURL = '/examination/resort/'.$exam->id.'/'.$exam->title.'#resort'.$resort->id;
        $fromName = '试卷求助：'.$parentResort->title;
        $fromURL = '/examination/resort/'.$exam->id.'/'.'#resort'.$parentResort->id;
        UserDynamic::dynamicAdd($parentResort->author_id,$parentResort->author,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加热度记录
        $b_id = 42;
        ExamTemperatureRecord::recordAdd($exam->id,$resort->author_id,$b_id,$createtime);
        // 通知原反对作者被接受
        User::find($resort->author_id)->notify(new ExamResortSupportAcceptedToUserNotification($resort));

        // 开启对协作组成员的通知
        $manage_id = $exam->manage_id;
        if(count($cooperation)){
            $crewArr = $cooperation->crews()->pluck('user_id')->toArray();
            $initiate_id = $cooperation->manage_id;
            array_push($crewArr, $manage_id);
            array_push($crewArr, $initiate_id); 
        }else{
            $crewArr = [];
            array_push($crewArr, $manage_id);
        }
        // 获取词条的关注用户
        // $focusUsers = $Exam->ExamFocus()->pluck('user_id')->toArray();
        // 合并协作组
        $users = array_unique($crewArr);
        $usersToNotification = User::whereIn('id',$users)->get();
        // User::whereIn('id',$users)->notify(new InterestSpecialtyExamAdd($result));
        Notification::send($usersToNotification, new ExamResortSupportAcceptedNotification($resort));
    }
}
