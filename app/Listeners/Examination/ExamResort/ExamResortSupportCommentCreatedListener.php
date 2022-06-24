<?php

namespace App\Listeners\Examination\ExamResort;

use App\Events\Examination\ExamResort\ExamResortSupportCommentCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Examination\ExamResort\ExamResortSupportCommentCreatedNotification;
use App\Home\Examination\ExamResort\ExamResortSupportComment;
use App\Home\Examination\ExamResort;
use App\Home\Examination\ExamResort\ExamResortEvent;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use Carbon\Carbon;
use App\Models\User;

class ExamResortSupportCommentCreatedListener
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
     * @param  ExamResortSupportCommentCreatedEvent  $event
     * @return void
     */
    public function handle(ExamResortSupportCommentCreatedEvent $event)
    {
        //原则上来说，对求助话题的帮助，只需要通知求助者即可,该comment是普通评论
        $comment = $event->examResortSupportComment;
        $resort = ExamResort::find($comment->resortId);
        $createtime = Carbon::now();
        ExamResortEvent::resortEventAdd($resort->exam_id,$comment->author_id,$comment->getAuthor->username,'发布了帮助内容回复：<'.$comment->title.'>。');
        //积分和成长值+50
        User::expAndGrowValue($comment->author_id,'10','10');
        // 不需要用户动态
        // 词条添加热度记录
        $b_id = 44;
        ExamTemperatureRecord::recordAdd($comment->exam_id,$comment->author_id,$b_id,$createtime);
        // 通知被回复用户
        if($comment->pid == 0){
            // 如果是pid为0，需要回复resort的作者
            $user_id = ExamResort::find($comment->resortId)->author_id;
        }else{
            $user_id = ExamResortSupportComment::find($comment->pid)->author_id;
        }
        User::find($user_id)->notify(new ExamResortSupportCommentCreatedNotification($comment));
    }
}
