<?php

namespace App\Listeners\Publication\ArticleDebate\ArticleDebateClear;

use App\Events\Publication\ArticleDebate\ArticleDebateClear\ArticleDebateTimeOutByRefereeClearedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Publication\ArticleDebate\ArticleDebateClear\ArticleDebateTimeOutByRefereeClearedNotification;
use App\Notifications\Publication\ArticleDebate\ArticleDebateClear\ArticleDebateTimeOutByRefereeClearedToUserNotification;
use App\Notifications\Publication\ArticleDebate\ArticleDebateClear\ArticleDebateTimeOutByRefereeClearedToRefereeNotification;
use App\Home\Publication\ArticleReview\ArticleReviewOpponent;
use App\Home\Publication\ArticleReview\ArticleReviewEvent;
use App\Home\Publication\ArticleDiscussion\ArticleDiscussionEvent;
use App\Home\Publication\ArticleDiscussion\ArticleOpponent;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleDebate\ArticleDebateEvent;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\Classification;
use App\Home\Announcement;
use App\Home\Publication\ArticleDynamic;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleDebateTimeOutByRefereeClearedListener
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
     * @param  ArticleDebateTimeOutByRefereeClearedEvent  $event
     * @return void
     */
    public function handle(ArticleDebateTimeOutByRefereeClearedEvent $event)
    {
        // 裁判超时，对裁判惩罚
        //攻辩自动结算，写入事件，通知双方，公告，通知兴趣用户攻辩结束
        //著作辩论创建成功后，写入辩论事件，写入公告，通知所有与本著作有关的用户，通知辩论接收用户，写入相关事件
        $debate = $event->articleDebate;
        if($debate->type == '1'){
            //添加事件到讨论事件表,$type==1表示该辩论来源是评审计划的反对
            $review_id = ArticleReview::find(ArticleReviewOpponent::find($debate->type_id)->rid);
            ArticleReviewEvent::reviewEventAdd($review_id,$debate->Aauthor_id,$debate->Aauthor,'向'.$debate->Bauthor.'发起的攻辩:['.$debate->title.']已经结算。');
            //改变反对状态为已转辩论
            ArticleReviewOpponent::where('id',$debate->type_id)->update([
                'status' => '3',
            ]);
        }elseif($debate->type == '2'){
            //添加事件到讨论事件表，$type==2是来源著作讨论的反对
            ArticleDiscussionEvent::discussionEventAdd($debate->aid,$debate->Aauthor_id,$debate->Aauthor,'向'.$debate->Bauthor.'发起的攻辩:['.$debate->title.']已经结算。');
            //改变反对状态为已转辩论
            ArticleOpponent::where('id',$debate->type_id)->update([
                'status' => '3',
            ]);
        }
        $article = Article::find($debate->aid);
        $manage_id = $article->manage_id;
        //添加事件到辩论事件表
        ArticleDebateEvent::debateEventAdd($debate->id,$debate->Aauthor_id,$debate->Aauthor,'攻辩已经结束（裁判结算超时，自动结算）。');
        //计算双方的奖励和裁判工资
        $rewardA = $debate->ARedstars*5 - $debate->ABlackstars*10;
        $rewardB = $debate->BRedstars*5 - $debate->BBlackstars*10;
        $rewardR = $debate->RRedstars*5 - $debate->RBlackstars*10-1000;
        // 补充对裁判的不诚信记录**************************************************************************
        if($debate->victory == '1'){
            $rewardA += '100';
            $creator = $debate->Aauthor;
            $creator_id = $debate->Aauthor_id;
            // 添加热度记录
            $b_id = 66;
            ArticleTemperatureRecord::recordAdd($debate->aid,$debate->Aauthor_id,$b_id,$createtime);
        }elseif($debate->victory == '2'){
            $rewardB += '100';
            $creator = $debate->Bauthor;
            $creator_id = $debate->Bauthor_id;
            // 添加热度记录
            $b_id = 67;
            ArticleTemperatureRecord::recordAdd($debate->aid,$debate->Bauthor_id,$b_id,$createtime);
        }
        User::expAndGrowValue($debate->Aauthor_id,$rewardA,$rewardA);
        User::expAndGrowValue($debate->Bauthor_id,$rewardB,$rewardB);
        User::expAndGrowValue($debate->referee_id,$rewardR,$rewardR);
        // 发布公告，1代表百科2代表著作，3代表辩论
        Announcement::announcementAdd('2','3','著作《'.$article->title.'攻辩<'.$debate->title.'>已经结束[裁判结算超时，自动结算]。','/publication/debate/'.$article->id.'/'.$article->title.'?type='.$debate->type.'&type_id='.$debate->type_id);
        // 攻辩结束不需要添加到用户动态
        $objectName = $debate->title;
        $objectURL = '/publication/debate/'.$article->id.'/'.$article->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        $createtime = Carbon::now();
        // 添加事件到著作动态
        $articleBehavior = '攻辩结束';
        ArticleDynamic::dynamicAdd($article->id,$article->title,$articleBehavior,$objectName,$objectURL,$createtime);
        // 添加热度记录
        $b_id = 68;
        ArticleTemperatureRecord::recordAdd($debate->aid,$debate->referee_id,$b_id,$createtime);
        // 发布通知
        // 发送通知给辩论参与人员
        User::find($debate->Aauthor_id)->notify(new ArticleDebateTimeOutByRefereeClearedToUserNotification($debate));
        User::find($debate->Bauthor_id)->notify(new ArticleDebateTimeOutByRefereeClearedToUserNotification($debate));
        User::find($debate->referee_id)->notify(new ArticleDebateTimeOutByRefereeClearedToRefereeNotification($debate));
        // 获取著作母专业兴趣人员
        $interestUsers = Classification::find($article->cid)->getInterestUsers()->pluck('user_id')->toArray();
        // 获取协作成员
        array_push($interestUsers, $manage_id);
        // 获取著作的关注用户
        $focusUsers = $article->articleFocus()->pluck('user_id')->toArray();
        $participateUsers = $debate->getStars->pluck('user_id')->toArray();
        $focusUsers = array_merge($focusUsers,$participateUsers);
        // 合并所有通知组用户
        $allUsers = array_unique(array_merge($focusUsers,$interestUsers));
        $usersToNotification = User::whereIn('id',$allUsers)->get();
        // 发送通知
        Notification::send($usersToNotification, new ArticleDebateTimeOutByRefereeClearedNotification($debate));
    }
}
