<?php

namespace App\Listeners\Publication\ArticleDebate;

use App\Events\Publication\ArticleDebate\ArticleDebateCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Publication\ArticleDebate\ArticleDebateCreatedNotification;
use App\Notifications\Publication\ArticleDebate\ArticleDebateCreatedToUserNotification;
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
use App\Home\Publication\Article\ArticleDynamic;
use App\Home\UserDynamic;
use Carbon\Carbon;
use App\Models\User;

class ArticleDebateCreatedListener
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
     * @param  ArticleDebateCreatedEvent  $event
     * @return void
     */
    public function handle(ArticleDebateCreatedEvent $event)
    {
        //辩论创建成功后，写入辩论事件，写入公告，通知所有与本词条有关的用户，通知辩论接收用户，写入相关事件
        $debate = $event->articleDebate;
        if($debate->type == '1'){
            //添加事件到讨论事件表,$type==1表示该辩论来源是评审计划的反对
            $review_id = ArticleReview::find(ArticleReviewOpponent::find($debate->type_id)->rid);
            ArticleReviewEvent::reviewEventAdd($review_id,$debate->Aauthor_id,$debate->Aauthor,'向'.$debate->Bauthor.'发起了攻辩:['.$debate->title.']。');
            //改变反对状态为已转辩论
            ArticleReviewOpponent::where('id',$debate->type_id)->update([
                'recipient' => $debate->Aauthor,
                'recipient_id' => $debate->Aauthor_id,
                'status' => '4',
            ]);
        }elseif($debate->type == '2'){
            //添加事件到讨论事件表，$type==2是来源词条讨论的反对
            ArticleDiscussionEvent::discussionEventAdd($debate->aid,$debate->Aauthor_id,$debate->Aauthor,'向'.$debate->Bauthor.'发起了攻辩:['.$debate->title.']。');
            //改变反对状态为已转辩论
            ArticleOpponent::where('id',$debate->type_id)->update([
                'recipient' => $debate->Aauthor,
                'recipient_id' => $debate->Aauthor_id,
                'status' => '4',
            ]);
        }
        $article = Article::find($debate->aid);
        $manage_id = $article->manage_id;
        //添加事件到辩论事件表
        ArticleDebateEvent::debateEventAdd($debate->id,$debate->Aauthor_id,$debate->Aauthor,'向'.$debate->Bauthor.'发起了攻辩:<'.$debate->title.'>。');
        //发表了有效的讨论后，积分和成长值+100
        User::expAndGrowValue($debate->Aauthor_id,'100','100');
        // 发布公告，1代表百科2代表著作，3代表辩论
        Announcement::announcementAdd('2','3','著作《'.$article->title.'》新增辩论<'.$debate->title.'>。','/publication/debate/'.$article->id.'/'.$article->title.'?type='.$debate->type.'&type_id='.$debate->type_id,$debate->created_at);
        // 添加事件到用户动态
        $behavior = '发起了攻辩：';
        $objectName = $debate->title;
        $objectURL = '/publication/debate/'.$article->id.'/'.$article->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        $fromName = '著作：'.$article->title;
        $fromURL = '/publication/reading/'.$article->id.'/'.$article->title;
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($debate->Aauthor_id,$debate->Aauthor,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 添加事件到词条动态
        $articleBehavior = '新增攻辩';
        ArticleDynamic::dynamicAdd($article->id,$article->title,$articleBehavior,$objectName,$objectURL,$createtime);
        // 添加热度记录
        $b_id = 59;
        ArticleTemperatureRecord::recordAdd($debate->aid,$debate->Aauthor_id,$b_id,$createtime);
        // 发布通知
        // 发送通知给辩论接收人员
        User::find($debate->Bauthor_id)->notify(new ArticleDebateCreatedToUserNotification($debate));
        // 获取词条母专业兴趣人员
        $interestUsers = Classification::find($article->cid)->getInterestUsers()->pluck('user_id')->toArray();
        // 获取协作成员
        array_push($interestUsers, $manage_id);
        // 获取词条的关注用户
        $focusUsers = $article->articleFocus()->pluck('user_id')->toArray();
        // 合并所有通知组用户
        $allUsers = array_unique(array_merge($focusUsers,$interestUsers));
        $usersToNotification = User::whereIn('id',$allUsers)->get();
        // 发送通知
        Notification::send($usersToNotification, new ArticleDebateCreatedNotification($debate));
    }
}
