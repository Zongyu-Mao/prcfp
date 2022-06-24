<?php

namespace App\Listeners\Personnel\Inform\InformOperate;

use App\Events\Personnel\Inform\InformOperate\JudgementInformOperateRecordAddedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Personnel\JudgementInform\JudgementInformPassedNotification;
use App\Notifications\Personnel\JudgementInform\JudgementInformPassedToFailureNotification;
use App\Notifications\Personnel\JudgementInform\JudgementInformRejectedNotification;
use App\Notifications\Personnel\JudgementInform\JudgementInformRejectedToFailureNotification;
use App\Home\Personnel\JudgementInform;
use App\Home\Personnel\JudgementInform\JudgementInformOperateRecord;
use App\Home\Personnel\JudgementInform\JudgementInformEvent;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\Personnel\MedalSuit;
use App\Home\Personnel\Inform\PunishRecord;
use Carbon\Carbon;
use App\Models\User;

class JudgementInformOperateRecordAddedListener
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
     * @param  JudgementInformOperateRecordAddedEvent  $event
     * @return void
     */
    public function handle(JudgementInformOperateRecordAddedEvent $event)
    {
        // 写入举报信息操作记录后，判断是否需要结束该举报内容
        $record = $event->judgementInformOperateRecord;
        $inform = JudgementInform::find($record->inform_id);
        // 写入举报事件
        if($record->standpoint==1){
            $content = '通过了本次举报内容';
        }elseif($record->standpoint==2){
           $content = '驳回了本次举报内容'; 
        }
        
        $createtime = Carbon::now();
        JudgementInformEvent::informEventAdd($inform->id,$record->operator_id,$content,$createtime);
        // 需要的单方管理员数量
        $need = ceil($inform->weight/2);
        $need>7 ? $need=7:$need=$need;
        // 为了测试，使用1个管理员通过
        // $need<2 ? $need=2:$need=$need;
        $pass = count(JudgementInformOperateRecord::where([['inform_id',$inform->id],['standpoint',1]])->get());
        $reject = count(JudgementInformOperateRecord::where([['inform_id',$inform->id],['standpoint',2]])->get());
        // 举报结果需要公示
        if($pass >= $need && $inform->status==0){
            // 举报内容通过，举报成立
            // 举报成立时，对象用户发放惩戒章
            $punish_id = $inform->object_user_id;
            $medals = $inform->getMedals;
            $endtime = Carbon::now()->addMonths(3);
            // JudgementInform
            $type = 2;
            $remark = '举报信息已经通过';
            foreach($medals as $medal){
                PunishRecord::punishRecordAdd($medal->id,$punish_id,$inform->id,$type,$endtime,$createtime);
            }
            // 修改inform状态为通过
            $status = 1;
            JudgementInform::updateStatus($inform->id,$status);
            // 词条添加热度记录**********************************************************
            $b_id = 22;
            switch($inform->scope)
            {
                case 1:
                // 评审的中立发言
                $obj_id = EntryReviewDiscussion::find($inform->ground_id)->getReview->getEntry->id;
                // 词条添加热度记录
                $b_id = 34;
                EntryTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 2:
                // 评审的支持发言
                $obj_id = EntryReviewDiscussion::find($inform->ground_id)->getReview->getEntry->id;
                // 词条添加热度记录
                $b_id = 34;
                EntryTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 3:
                $obj_id = EntryReviewAdvise::find($inform->ground_id)->getReview->getEntry->id;
                // 词条添加热度记录
                $b_id = 33;
                EntryTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 4:
                $obj_id = EntryReviewOpponent::find($inform->ground_id)->getReview->getEntry->id;
                // 词条添加热度记录
                $b_id = 32;
                EntryTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 5:
                // 求助内容
                $obj_id = EntryResort::find($inform->ground_id)->getEntry->id;
                // 词条添加热度记录
                $b_id = 45;
                EntryTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 6:
                // 帮助内容
                $obj_id = EntryResort::find($inform->ground_id)->getEntry->id;
                // 词条添加热度记录
                $b_id = 46;
                EntryTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 7:
                // 主内容讨论内容
                $obj_id = EntryDiscussion::find($inform->ground_id)->getEntry->id;
                // 词条添加热度记录
                $b_id = 58;
                EntryTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 8:
                // 主内容建议内容
                $obj_id = EntryAdvise::find($inform->ground_id)->getEntry->id;
                // 词条添加热度记录
                $b_id = 57;
                EntryTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 9:
                // 主内容反对内容
                $obj_id = EntryOpponent::find($inform->ground_id)->getEntry->id;
                // 词条添加热度记录
                $b_id = 56;
                EntryTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 10:
                // 攻方发言注意id可能相同！！
                $obj_id = EntryDebate::find($inform->ground_id)->getEntry->id;
                // 词条添加热度记录
                $b_id = 71;
                EntryTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 11:
                // 辩方发言注意区别，同一个id
                $obj_id = EntryDebate::find($inform->ground_id)->getEntry->id;
                // 词条添加热度记录
                $b_id = 72;
                EntryTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 12:
                // 裁判发言，id可能相同
                $obj_id = EntryDebate::find($inform->ground_id)->getEntry->id;
                // 词条添加热度记录
                $b_id = 73;
                EntryTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 13:
                // 著作评审的中立发言
                $obj_id = ArticleReviewDiscussion::find($inform->ground_id)->getReviewt->getArticle->id;
                // 著作添加热度记录
                $b_id = 34;
                ArticleTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 14:
                // 评审的支持发言
                $obj_id = ArticleReviewDiscussion::find($inform->ground_id)->getReview->getArticle->id;
                // 著作添加热度记录
                $b_id = 34;
                ArticleTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 15:
                // 著作评审建议发言
                $obj_id = ArticleReviewAdvise::find($inform->ground_id)->getReview->getArticle->id;
                // 著作添加热度记录
                $b_id = 33;
                ArticleTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 16:
                // 评审反对意见
                $obj_id = ArticleReviewOpponent::find($inform->ground_id)->getReview->getArticle->id;
                // 著作添加热度记录
                $b_id = 32;
                ArticleTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 17:
                // 求助内容
                $obj_id = ArticleResort::find($inform->ground_id)->getArticle->id;
                // 著作添加热度记录
                $b_id = 45;
                ArticleTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 18:
                // 帮助内容
                $obj_id = ArticleResort::find($inform->ground_id)->getArticle->id;
                // 著作添加热度记录
                $b_id = 46;
                ArticleTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 19:
                // 主内容讨论内容
                $obj_id = ArticleDiscussion::find($inform->ground_id)->getArticle->id;
                // 著作添加热度记录
                $b_id = 58;
                ArticleTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 20:
                // 主内容建议内容
                $obj_id = ArticleAdvise::find($inform->ground_id)->getArticle->id;
                // 著作添加热度记录
                $b_id = 57;
                ArticleTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 21:
                // 主内容反对内容
                $obj_id = ArticleOpponent::find($inform->ground_id)->getArticle->id;
                // 著作添加热度记录
                $b_id = 56;
                ArticleTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 22:
                // 攻方发言注意id可能相同！！
                $obj_id = ArticleDebate::find($inform->ground_id)->getArticle->id;
                // 著作添加热度记录
                $b_id = 71;
                ArticleTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 23:
                // 辩方发言注意区别，同一个id
                $obj_id = ArticleDebate::find($inform->ground_id)->getArticle->id;
                // 著作添加热度记录
                $b_id = 72;
                ArticleTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 24:
                // 裁判发言，id可能相同
                $obj_id = ArticleDebate::find($inform->ground_id)->getArticle->id;
                // 著作添加热度记录
                $b_id = 73;
                ArticleTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 25:
                // 评审的中立发言
                $obj_id = ExamReviewDiscussion::find($inform->ground_id)->getReview->getExam->id;
                // 试卷添加热度记录
                $b_id = 34;
                ExamTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 26:
                // 评审的支持发言
                $obj_id = ExamReviewDiscussion::find($inform->ground_id)->getReview->getExam->id;
                // 试卷添加热度记录
                $b_id = 34;
                ExamTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 27:
                $obj_id = ExamReviewAdvise::find($inform->ground_id)->getReview->getExam->id;
                // 试卷添加热度记录
                $b_id = 33;
                ExamTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 28:
                $obj_id = ExamReviewOpponent::find($inform->ground_id)->getReview->getExam->id;
                // 试卷添加热度记录
                $b_id = 32;
                ExamTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 29:
                // 求助内容
                $obj_id = ExamResort::find($inform->ground_id)->getExam->id;
                // 试卷添加热度记录
                $b_id = 45;
                ExamTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 30:
                // 帮助内容
                $obj_id = ExamResort::find($inform->ground_id)->getExam->id;
                // 试卷添加热度记录
                $b_id = 46;
                ExamTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 31:
                // 主内容讨论内容
                $obj_id = ExamDiscussion::find($inform->ground_id)->getExam->id;
                // 试卷添加热度记录
                $b_id = 58;
                ExamTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 32:
                // 主内容建议内容
                $obj_id = ExamAdvise::find($inform->ground_id)->getExam->id;
                // 试卷添加热度记录
                $b_id = 57;
                ExamTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 33:
                // 主内容反对内容
                $obj_id = ExamOpponent::find($inform->ground_id)->getExam->id;
                // 试卷添加热度记录
                $b_id = 56;
                ExamTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 34:
                // 攻方发言注意id可能相同！！
                $obj_id = ExamDebate::find($inform->ground_id)->getExam->id;
                // 试卷添加热度记录
                $b_id = 71;
                ExamTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 35:
                // 辩方发言注意区别，同一个id
                $obj_id = ExamDebate::find($inform->ground_id)->getExam->id;
                // 试卷添加热度记录
                $b_id = 72;
                ExamTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 36:
                // 裁判发言，id可能相同
                $obj_id = ExamDebate::find($inform->ground_id)->getExam->id;
                // 试卷添加热度记录
                $b_id = 73;
                ExamTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;

            }
            // 发送通知给两方
            User::find($inform->author_id)->notify(new JudgementInformPassedNotification($inform));
            User::find($inform->object_user_id)->notify(new JudgementInformPassedToFailureNotification($inform));
        }else if($reject >= $need && $inform->status==0){
            // 举报驳回时，发起用户发放反举报章
            $punish_id = $inform->author_id;
            // 恶意举报惩戒章
            $suit = MedalSuit::where('type',3)->get();
            $medals = $suit->getMedals;
            $weight = $inform->weight;
            foreach($medals as $medal){
                if($weight > $medal->weight && $weight-$medal->weight < 10){
                        $punishMedal = $medal;
                    }
            }
            $endtime = Carbon::now()->addMonths(3);
            $type = 2;
            $remark = '举报信息已经驳回';
            // 修改inform状态为驳回
            $status = 2;
            JudgementInform::updateStatus($inform->id,$status);
            // 写入惩戒结果
            PunishRecord::punishRecordAdd($medal->id,$punish_id,$inform->id,$type,$endtime,$createtime);
            // 发送通知给两方
            User::find($inform->object_user_id)->notify(new JudgementInformRejectedNotification($inform));
            User::find($inform->author_id)->notify(new JudgementInformRejectedToFailureNotification($inform));
        }
        // 过了要删除，不过会不会过呢。~
        // if($pass>$need || $reject>$need){
        //     JudgementInformOperateRecord::where('id',$record)->delete();
        // }
    }
}
