<?php

namespace App\Listeners\Publication\ArticleDebate\ArticleDebateClosed;

use App\Events\Publication\ArticleDebate\ArticleDebateClosed\ArticleDebateTimeOutClosedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Publication\ArticleDebate\ArticleDebateClosed\ArticleDebateTimeOutClosedNotification;
use App\Notifications\Publication\ArticleDebate\ArticleDebateClosed\ArticleDebateTimeOutClosedToUserNotification;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleDebate\ArticleDebateEvent;
use App\Home\Publication\ArticleReview\ArticleReviewEvent;
use App\Home\Publication\ArticleReview\ArticleReviewOpponent;
use App\Home\Publication\ArticleDiscussion\ArticleDiscussionEvent;
use App\Home\Publication\ArticleDiscussion\ArticleOpponent;
use App\Home\Classification;
use App\Home\Announcement;
use App\Home\Publication\Article\ArticleDynamic;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleDebateTimeOutClosedListener
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
     * @param  ArticleDebateTimeOutClosedEvent  $event
     * @return void
     */
    public function handle(ArticleDebateTimeOutClosedEvent $event)
    {
        $debate = $event->articleDebate;
        $article = Article::find($debate->aid);
        $manage_id = $article->manage_id;
        //超时分为攻方原因和辩方原因
        if($debate->status == '2'){
            // status为2，是由于攻方原因导致
            // 1、导致攻辩失败方扣除成长值1000
            User::expAndGrowValue($debate->Aauthor_id,0,-1000);
            // 2发布事件和公告
            // 3添加事件到辩论事件表
            ArticleDebateEvent::debateEventAdd($debate->id,$debate->Aauthor_id,$debate->Aauthor,'攻辩:<'.$debate->title.'>由于攻方['.$debate->Aauthor.']回复超时关闭。');
            // 发表了有效的讨论后，积分和成长值+100
            // User::expAndGrowValue($debate->Aauthor_id,'100','100');
            // 4发布公告，1代表百科2代表著作，3代表辩论
            Announcement::announcementAdd('2','3','著作《'.$article->title.'》辩论<'.$debate->title.'>由于攻方回复超时已经关闭。','/publication/debate/'.$article->id.'/'.$article->title.'?type='.$debate->type.'&type_id='.$debate->type_id,$debate->updated_at);
            // 5添加事件到用户动态
            $behavior = '[攻方]回复超时导致结束攻辩：';
            $objectName = $debate->title;
            $objectURL = '/publication/debate/'.$article->id.'/'.$article->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
            $fromName = '著作：'.$article->title;
            $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
            $createtime = Carbon::now();
            UserDynamic::dynamicAdd($debate->Aauthor_id,$debate->Aauthor,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
            // 6添加事件到著作动态
            $articleBehavior = '攻辩超时关闭';
            ArticleDynamic::dynamicAdd($article->id,$article->title,$articleBehavior,$objectName,$objectURL,$createtime);
            // 添加热度记录
            $b_id = 64;
            ArticleTemperatureRecord::recordAdd($debate->aid,$debate->Aauthor_id,$b_id,$createtime);
            // 发布通知
            // 发送通知给辩论攻方接收人员
            User::find($debate->Bauthor_id)->notify(new ArticleDebateTimeOutClosedToUserNotification($debate));
        }elseif($debate->status == '3'){
            // status为3，是由于辩方原因导致
            // 1、导致攻辩失败方扣除成长值1000
            User::expAndGrowValue($debate->Bauthor_id,0,-1000);
            // 2发布事件和公告
            // 3添加事件到辩论事件表
            ArticleDebateEvent::debateEventAdd($debate->id,$debate->Bauthor_id,$debate->Bauthor,'攻辩:<'.$debate->title.'>由于辩方['.$debate->Bauthor.']回复超时关闭。');
            // 发表了有效的讨论后，积分和成长值+100
            // User::expAndGrowValue($debate->Aauthor_id,'100','100');
            // 4发布公告，1代表百科2代表著作，3代表辩论
            Announcement::announcementAdd('2','3','著作《'.$article->title.'》辩论<'.$debate->title.'>由于辩方回复超时已经关闭。','/publication/debate/'.$article->id.'/'.$article->title.'?type='.$debate->type.'&type_id='.$debate->type_id,$debate->created_at);
            // 5添加事件到用户动态
            $behavior = '[辩方]回复超时导致结束攻辩：';
            $objectName = $debate->title;
            $objectURL = '/home/publication/debate/'.$article->id.'/'.$article->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
            $fromName = '著作：'.$article->title;
            $fromURL = '/home/publication/reading/'.$article->id.'/'.$article->title;
            $createtime = Carbon::now();
            UserDynamic::dynamicAdd($debate->Bauthor_id,$debate->Bauthor,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
            // 6添加事件到著作动态
            $articleBehavior = '攻辩超时关闭';
            ArticleDynamic::dynamicAdd($article->id,$article->title,$articleBehavior,$objectName,$objectURL,$createtime);
            // 添加热度记录
            $b_id = 65;
            ArticleTemperatureRecord::recordAdd($debate->aid,$debate->Bauthor_id,$b_id,$createtime);
            // 发布通知
            // 发送通知给辩论攻方接收人员
            User::find($debate->Bauthor_id)->notify(new ArticleDebateTimeOutClosedToUserNotification($debate));
        }
        // 变更攻辩归口状态为攻辩失败
        if($debate->type == '1'){
            //添加事件到讨论事件表,$type==1表示该辩论来源是评审计划的反对
            $review_id = ArticleReview::find(ArticleReviewOpponent::find($debate->type_id)->rid);
            ArticleReviewEvent::reviewEventAdd($review_id,$debate->Aauthor_id,$debate->Aauthor,'发起的攻辩:['.$debate->title.']已经失败[超时回复关闭]。');
            //改变反对状态为攻辩失败
            ArticleReviewOpponent::where('id',$debate->type_id)->update([
                'status' => '5',
            ]);
        }elseif($debate->type == '2'){
            //添加事件到讨论事件表，$type==2是来源著作讨论的反对
            ArticleDiscussionEvent::discussionEventAdd($debate->aid,$debate->Aauthor_id,$debate->Aauthor,'发起的攻辩:['.$debate->title.']已经失败[超时回复关闭]。');
            //改变反对状态为攻辩失败
            ArticleOpponent::where('id',$debate->type_id)->update([
                'status' => '5',
            ]);
        }

        // 获取著作母专业兴趣人员
        $interestUsers = Classification::find($article->cid)->getInterestUsers()->pluck('user_id')->toArray();
        // 获取协作成员
        array_push($interestUsers, $manage_id);
        // 获取著作的关注用户
        $focusUsers = $article->articleFocus()->pluck('user_id')->toArray();
        // 合并所有通知组用户
        $allUsers = array_unique(array_merge($focusUsers,$interestUsers));
        $usersToNotification = User::whereIn('id',$allUsers)->get();
        // 发送通知
        Notification::send($usersToNotification, new ArticleDebateTimeOutClosedNotification($debate));
    }
}
