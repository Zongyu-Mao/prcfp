<?php

namespace App\Listeners\Examination\ExtendedReading;

use App\Events\Examination\ExtendedReading\ExamExtendedArticleAddedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\ExamCooperation\ExamCooperationEvent;
use App\Home\Examination\Exam;
use App\Home\Publication\Article;
use App\Home\Examination\Exam\ExamDynamic;
use App\Home\Publication\Article\ArticleDynamic;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ExamExtendedArticleAddedListener
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
     * @param  ExamExtendedArticleAddedEvent  $event
     * @return void
     */
    public function handle(ExamExtendedArticleAddedEvent $event)
    {
        // 得到延伸、被延伸试卷,写入用户动态和协作事件，不需要通知
        $extend = $event->examExtendedArticle;
        $exam = Exam::find($extend->exam_id);
        $extended = Article::find($extend->extended_id);
        //该处所要处理的是延伸阅读添加后，通知本词条相关用户该信息
        $cooperation = ExamCooperation::find($exam->cooperation_id);
        $user = auth('api')->user();
        // 添加协作事件
        ExamCooperationEvent::cooperationEventAdd($cooperation->id,$user->id,$user->username,'添加了著作延伸阅读《'.$extended->title.'》。');
        // 添加事件到用户动态
        $behavior = '添加了延伸(著作)阅读：';
        $objectName = $extended->title;
        $objectURL = '/publication/reading/'.$extended->id.'/'.$extended->title;
        $fromName = '试卷：'.$exam->title;
        $fromURL = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($user->id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到著作动态
        $examBehavior = '已经添加延伸阅读';
        ExamDynamic::dynamicAdd($exam->id,$exam->title,$examBehavior,$objectName,$objectURL,$createtime);
        // 添加事件到被延伸词条动态
        $extendBehavior = '已经被添加为延伸阅读。';
        ArticleDynamic::dynamicAdd($extended->id,$extended->title,$extendBehavior,$fromName,$fromURL,$createtime);
        // 主动词条添加热度记录
        $b_id = 10;
        ExamTemperatureRecord::recordAdd($exam->id,$user->id,$b_id,$createtime);
        // 被延伸著作添加热度记录
        $be_id = 12;
        ArticleTemperatureRecord::recordAdd($extended->id,$user->id,$be_id,$createtime);
    }
}
