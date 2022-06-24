<?php

namespace App\Listeners\Personnel\Inform\InformOperate;

use App\Events\Personnel\Inform\InformOperate\MessageInformOperateRecordAddedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Personnel\MessageInform\MessageInformPassedNotification;
use App\Notifications\Personnel\MessageInform\MessageInformPassedToFailureNotification;
use App\Notifications\Personnel\MessageInform\MessageInformRejectedNotification;
use App\Notifications\Personnel\MessageInform\MessageInformRejectedToFailureNotification;
use App\Home\Personnel\MessageInform;
use App\Home\Personnel\MessageInform\MessageInformOperateRecord;
use App\Home\Personnel\MessageInform\MessageInformEvent;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\Personnel\MedalSuit;
use App\Home\Personnel\Inform\PunishRecord;
use Carbon\Carbon;
use App\Models\User;

class MessageInformOperateRecordAddedListener
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
     * @param  MessageInformOperateRecordAddedEvent  $event
     * @return void
     */
    public function handle(MessageInformOperateRecordAddedEvent $event)
    {
        // 写入举报信息操作记录后，判断是否需要结束该举报内容

        $record = $event->messageInformOperateRecord;
        $inform = MessageInform::find($record->inform_id);
        $scope = $inform->scope;
        // 写入举报事件
        if($record->standpoint==1){
            $content = '通过了本次举报内容';
        }elseif($record->standpoint==2){
           $content = '驳回了本次举报内容'; 
        }
        
        $createtime = Carbon::now();
        MessageInformEvent::informEventAdd($inform->id,$record->operator_id,$content,$createtime);
        // 需要的单方管理员数量
        $need = ceil($inform->weight/3);
        $need>7 ? $need=7:$need=$need;
        // 为了测试，使用1个管理员通过
        // $need<2 ? $need=2:$need=$need;
        $pass = count(MessageInformOperateRecord::where([['inform_id',$inform->id],['standpoint',1]])->get());
        $reject = count(MessageInformOperateRecord::where([['inform_id',$inform->id],['standpoint',2]])->get());
        // 举报结果需要公示
        if($pass >= $need  && $inform->status==0){
            // 举报内容通过，举报成立
            // 举报成立时，对象用户发放惩戒章
            $punish_id = $inform->object_user_id;
            $medals = $inform->getMedals;
            $endtime = Carbon::now()->addMonths(3);
            // MessageInform
            $type = 3;
            $remark = '举报信息已经通过';
            foreach($medals as $medal){
                PunishRecord::punishRecordAdd($medal->id,$punish_id,$inform->id,$type,$endtime,$createtime);
            }
            // 修改inform状态为通过
            $status = 1;
            MessageInform::updateStatus($inform->id,$status);
            
            switch($scope)
            {
                case 1:
                // 协作留言
                $obj_id = EntryCooperationMessage::find($inform->ground_id)->getCooperation->getEntry->id;
                // 词条添加热度记录
                $b_id = 22;
                EntryTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 2:
                // 评审回复
                $obj_id = EntryReviewDiscussion::find($inform->ground_id)->getReview->getEntry->id;
                // 词条添加热度记录
                $b_id = 35;
                EntryTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 3:
                // 求助帮助留言
                $obj_id = EntryResortSupportComment::find($inform->ground_id)->getResort->getEntry->id;
                // 词条添加热度记录
                $b_id = 47;
                EntryTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 4:
                // 攻辩留言
                $obj_id = EntryDebateComment::find($inform->ground_id)->getDebate->getEntry->id;
                // 词条添加热度记录
                $b_id = 74;
                EntryTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 5:
                // 著作协作留言
                $obj_id = ArticleCooperationMessage::find($inform->ground_id)->getCooperation->getArticle->id;
                // 添加热度记录
                $b_id = 22;
                ArticleTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 6:
                // 著作评审留言
                $obj_id = ArticleReviewDiscussion::find($inform->ground_id)->getReview->getArticle->id;
                // 添加热度记录
                $b_id = 35;
                ArticleTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 7:
                // 著作求助帮助留言
                $obj_id = ArticleResortSupportComment::find($inform->ground_id)->getResort->getArticle->id;
                // 添加热度记录
                $b_id = 47;
                ArticleTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 8:
                // 著作攻辩留言
                $obj_id = ArticleDebateComment::find($inform->ground_id)->getDebate->getArticle->id;
                // 添加热度记录
                $b_id = 74;
                ArticleTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 9:
                // 试卷协作留言
                $obj_id = ExamCooperationMessage::find($inform->ground_id)->getCooperation->getExam->id;
                // 添加热度记录
                $b_id = 22;
                ExamTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 10:
                // 试卷评审留言
                $obj_id = ExamReviewDiscussion::find($inform->ground_id)->getReview->getExam->id;
                // 添加热度记录
                $b_id = 35;
                ExamTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 11:
                // 试卷求助帮助留言
                $obj_id = ExamResortSupportComment::find($inform->ground_id)->getResort->getExam->id;
                // 添加热度记录
                $b_id = 47;
                ExamTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 12:
                // 试卷攻辩留言
                $obj_id = ExamDebateComment::find($inform->ground_id)->getDebate->getExam->id;
                // 添加热度记录
                $b_id = 74;
                ExamTemperatureRecord::recordAdd($obj_id,$inform->author_id,$b_id,$createtime);
                break;
                case 13:
                // 组织的还灭想好
                $message = GroupDocComment::find($inform->ground_id);
                $parent = $message->getDebate;
                $url_obj = $parent->getExam;
                $remark = '试卷《'.$url_obj->title.'》的攻辩计划<'.$parent->title.'>';
                $url = '/examination/debate/'.$url_obj_id.'/'.$url_obj->title;
                break;
            }
            
            // 发送通知给两方
            User::find($inform->author_id)->notify(new MessageInformPassedNotification($inform));
            User::find($inform->object_user_id)->notify(new MessageInformPassedToFailureNotification($inform));
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
            $type = 3;
            $remark = '举报信息已经驳回';
            // 修改inform状态为通过
            $status = 2;
            MessageInform::updateStatus($inform->id,$status);
            // 写入惩戒结果
            PunishRecord::punishRecordAdd($medal->id,$punish_id,$inform->id,$type,$endtime,$createtime);
            // 发送通知给两方
            User::find($inform->object_user_id)->notify(new MessageInformRejectedNotification($inform));
            User::find($inform->author_id)->notify(new MessageInformRejectedToFailureNotification($inform));
        }
        // 过了要删除，不过会不会过呢。~
        // if($pass>$need || $reject>$need){
        //     MessageInformOperateRecord::where('id',$record)->delete();
        // }
    }
}
