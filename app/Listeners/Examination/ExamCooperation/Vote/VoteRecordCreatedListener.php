<?php

namespace App\Listeners\Examination\ExamCooperation\Vote;

use App\Events\Examination\ExamCooperation\Vote\VoteRecordCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Examination\ExamCooperation\ExamCooperationEvent;
use App\Home\Examination\ExamCooperation\ExamCooperationUser;
use App\Home\Examination\ExamCooperation\ExamCooperationVote;
use App\Home\Examination\ExamCooperation\ExamCooperationVoteRecord;
use App\Home\Examination\ExamCooperation;
use Carbon\Carbon;

class VoteRecordCreatedListener
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
     * @param  VoteRecordCreatedEvent  $event
     * @return void
     */
    public function handle(VoteRecordCreatedEvent $event)
    {
        // 有用户投票后，写入协作事件
        $record = $event->examCooperationVoteRecord;
        $vote = ExamCooperationVote::find($record->vote_id);
        $cooperation = ExamCooperation::find($vote->cooperation_id);
        $crew = $cooperation->crews()->pluck('user_id')->toArray();
        array_push($crew, $cooperation->manage_id);
        array_unique($crew);
        // 判断立场
        if($record->standpoint==1){
            $standpoint='同意';
        }elseif($record->standpoint==2){
            $standpoint='反对';
        }elseif($record->standpoint==3){
            $standpoint='中立';
        }
        //判断投票类型，1自定义协作事务2申请进组3弹劾组长4劝退组员
        if($vote->type==1){
            $voteEvent=$standpoint.'<'.$vote->initiate.'>'.'发起的小组事务投票：'.$vote->title;
            }elseif($vote->type==2){
                $voteEvent=$standpoint.'<'.$vote->initiate.'>'.'进入协作小组：'.$vote->title;
            }elseif($vote->type==3){
                $voteEvent=$standpoint.'<'.$vote->initiate.'>'.'发起的组长弹劾：'.$vote->title;
            }elseif($vote->type==4){
                $voteEvent=$standpoint.'<'.$vote->initiate.'>'.'发起的组员劝退：'.$vote->title;
            }
        //本次投票计入协作事件
        ExamCooperationEvent::cooperationEventAdd($vote->cooperation_id,$record->user_id,$record->username,$voteEvent);

        // 结束后判断投票是否达到结束条件，如果可以结算，直接结算。
        $agree=ExamCooperationVoteRecord::getAgreeNum($vote->id);
        $oppose=ExamCooperationVoteRecord::getOpposeNum($vote->id);
        $neutral=ExamCooperationVoteRecord::getNeutralNum($vote->id);
        $party=count($crew);
        if($agree/$party>='0.5' && $vote->status=='0'){
            ExamCooperationVote::where('id',$vote->id)->update([
                'status'=>'1',
                'remark'=>'本次投票由于支持人数过半通过。'
            ]);
            // 如果是进组的投票，需要将用户拉入小组，同样的，其他投票行为也需要另外的操作
                if($vote->type==2){
                    // 成功进入协作小组
                    ExamCooperationUser::cooperationMemberJoin($cooperation->id,$vote->initiate_id,Carbon::now());
                }elseif($vote->type==3){
                    // 弹劾组长成功，组长变为组员，弹劾者升级为组长，这个功能先做上，其实估计不能实现，所以也暂不考虑实现相关事件
                    ExamCooperationUser::where([['cooperation_id',$cooperation->id],['user_id',$vote->initiate_id]])->delete();
                    ExamCooperationUser::cooperationMemberJoin($cooperation->id,$cooperation->manage_id,$createtime);
                    // 升级组员为组长
                    ExamCooperation::where('id',$vote->cooperation_id)->update([
                        'manage_id'=>$vote->initiate_id,
                        'manager'=>$vote->initiate
                    ]);
                    ExamCooperationEvent::cooperationEventAdd($vote->cooperation_id,$vote->initiate_id,$vote->initiate,'弹劾组长成功。');
                }
            }elseif($oppose/$party>0.5 && $vote->status=='0'){
                ExamCooperationVote::where('id',$vote->id)->update([
                    'status'=>'2',
                    'remark'=>'本次投票由于反对人数过半关闭投票通道。'
                ]);
            }elseif($neutral/$party>0.5 && $vote->status=='0'){
                ExamCooperationVote::where('id',$vote->id)->update([
                    'status'=>'2',
                    'remark'=>'本次投票由于中立人数过半关闭投票通道。'
                ]);
            }
        // 最后，更新一下投票中的过期记录，这个，这个。。。先这样放着
        $examCooperationVote = ExamCooperationVote::where('cooperation_id',$vote->cooperation_id)->get();
        foreach($examCooperationVote as $value){
            if(Carbon::now()>$value->deadline && $value->status=='0'){
                ExamCooperationVote::where('id',$value->id)->update([
                    'status'=>'2',
                    'remark'=>'本次投票由于过期关闭投票通道。'
                ]);
            }
        }
    }
}
