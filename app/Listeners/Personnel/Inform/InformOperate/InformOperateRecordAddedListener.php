<?php

namespace App\Listeners\Personnel\Inform\InformOperate;

use App\Events\Personnel\Inform\InformOperate\InformOperateRecordAddedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Personnel\BasicInform\BasicInformPassedNotification;
use App\Notifications\Personnel\BasicInform\BasicInformPassedToFailureNotification;
use App\Notifications\Personnel\BasicInform\BasicInformRejectedNotification;
use App\Notifications\Personnel\BasicInform\BasicInformRejectedToFailureNotification;
use App\Home\Personnel\Inform;
use App\Home\Personnel\Inform\InformOperateRecord;
use App\Home\Personnel\Inform\InformEvent;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Personnel\Behavior;
use App\Home\Personnel\MedalSuit;
use App\Home\Personnel\Inform\PunishRecord;
use Carbon\Carbon;
use App\Models\User;

class InformOperateRecordAddedListener
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
     * @param  InformOperateRecordAddedEvent  $event
     * @return void
     */
    public function handle(InformOperateRecordAddedEvent $event)
    {
        // 写入举报信息操作记录后，判断是否需要结束该举报内容
        $record = $event->informOperateRecord;
        $inform = Inform::find($record->inform_id);
        // 写入举报事件
        if($record->standpoint==1){
            $content = '通过了本次举报内容';
        }elseif($record->standpoint==2){
           $content = '驳回了本次举报内容'; 
        }
        
        $createtime = Carbon::now();
        InformEvent::informEventAdd($inform->id,$record->operator_id,$content,$createtime);
        // 需要的单方管理员数量
        $need = ceil($inform->weight/2);
        $need>7 ? $need=7:$need=$need;
        // 为了测试，使用1个管理员通过
        // $need<2 ? $need=2:$need=$need;
        $pass = count(InformOperateRecord::where([['inform_id',$inform->id],['standpoint',1]])->get());
        $reject = count(InformOperateRecord::where([['inform_id',$inform->id],['standpoint',2]])->get());
        // 举报结果需要公示
        if($pass >= $need && $inform->status==0){
            // 举报内容通过，举报成立
            // 举报成立时，对象用户发放惩戒章
            $punish_id = $inform->object_user_id;
            $medals = $inform->getMedals;
            $endtime = Carbon::now()->addMonths(3);
            // basicInform
            $type = 1;
            $remark = '举报信息已经通过';
            foreach($medals as $medal){
                PunishRecord::punishRecordAdd($medal->id,$punish_id,$inform->id,$type,$endtime,$createtime);
            }
            // 修改inform状态为通过
            $status = 1;
            Inform::updateStatus($inform->id,$status);
            // 更改主内容热度,注意只有举报成功才会影响热度
            switch($inform->scope)
            {
                case 1:
                // 词条添加热度记录
                $b_id = 14;
                EntryTemperatureRecord::recordAdd($inform->ground,$record->operator_id,$b_id,$createtime);
                break;
                case 2:
                // 著作添加热度记录
                $b_id = 14;
                ArticleTemperatureRecord::recordAdd($inform->ground,$record->operator_id,$b_id,$createtime);
                break;
                case 3:
                // 著作添加热度记录
                // $b_id = Behavior::where('sort',14)->first()->id;
                $b_id=14;
                ExamTemperatureRecord::recordAdd($inform->ground,$record->operator_id,$b_id,$createtime);
                break;
                case 4:
                // 还没想好组织热度
                break;
            }
            // 发送通知给两方
            User::find($inform->author_id)->notify(new BasicInformPassedNotification($inform));
            User::find($inform->object_user_id)->notify(new BasicInformPassedToFailureNotification($inform));
        } else if($reject >= $need && $inform->status==0){
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
            $type = 1;
            $remark = '举报信息已经驳回';
            // 修改inform状态为驳回
            $status = 2;
            Inform::updateStatus($inform->id,$status);
            // 写入惩戒结果
            PunishRecord::punishRecordAdd($medal->id,$punish_id,$inform->id,$type,$endtime,$createtime);
            // 发送通知给两方
            User::find($inform->object_user_id)->notify(new BasicInformRejectedNotification($inform));
            User::find($punish_id)->notify(new BasicInformRejectedToFailureNotification($inform));
        }
        // 过了要删除，不过会不会过呢。~暂时不删除记录
        // if($pass>$need || $reject>$need){
        //     InformOperateRecord::where('id',$record->id)->delete();
        // }
    }
}
