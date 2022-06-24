<?php

namespace App\Listeners\Organization\Group\Vote;

use App\Events\Organization\Group\Vote\VoteRecordCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Organization\Group\GroupEvent;
use App\Home\Organization\Group\GroupUser;
use App\Home\Organization\Group\GroupVote;
use App\Home\Organization\Group\GroupVoteRecord;
use App\Home\Organization\Group;
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
        $record = $event->groupVoteRecord;
        $vote = GroupVote::find($record->vote_id);
        $group = Group::find($vote->gid);
        $crew = $group->members()->pluck('user_id')->toArray();
        array_push($crew, $group->manage_id);
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
            $voteEvent=$standpoint.'<'.$vote->initiate.'>'.'发起的组织事务投票：'.$vote->title;
            }elseif($vote->type==2){
                $voteEvent=$standpoint.'<'.$vote->initiate.'>'.'进入组织：'.$vote->title;
            }elseif($vote->type==3){
                $voteEvent=$standpoint.'<'.$vote->initiate.'>'.'发起的组长弹劾：'.$vote->title;
            }elseif($vote->type==4){
                $voteEvent=$standpoint.'<'.$vote->initiate.'>'.'发起的成员劝退：'.$vote->title;
            }
        //本次投票计入事件
        GroupEvent::groupEventAdd($vote->gid,$record->user_id,$record->username,$voteEvent);

        // 结束后判断投票是否达到结束条件，如果可以结算，直接结算。
        $agree=GroupVoteRecord::getAgreeNum($vote->id);
        $oppose=GroupVoteRecord::getOpposeNum($vote->id);
        $neutral=GroupVoteRecord::getNeutralNum($vote->id);
        $party=count($crew);
        if($vote->type==2)$party=1;//这里目前只需要通过或者反对人员进组，而又不知道后期会如何扩展组织，因此以下内容不变，只是这里偷懒一个人通过就可以了
        if($agree/$party>=0.5 && $vote->status==0){
            GroupVote::where('id',$vote->id)->update([
                'status'=>1,
                'remark'=>'通过。'
                // 'remark'=>'本次投票由于支持人数过半通过。'
            ]);
            // 如果是进组的投票，需要将用户拉入小组，同样的，其他投票行为也需要另外的操作
                if($vote->type==2){
                    // 成功进入组织
                    GroupUser::groupMemberJoin($vote->initiate_id,$group->id,Carbon::now());
                }elseif($vote->type==3){
                    // 弹劾组长成功，组长变为组员，弹劾者升级为组长，这个功能先做上，其实估计不能实现，所以也暂不考虑实现相关事件
                    GroupUser::where([['gid',$group->id],['user_id',$vote->initiate_id]])->delete();
                    GroupUser::groupMemberJoin($group->manage_id,$group->id,Carbon::now());
                    // 升级组员为组长
                    Group::where('id',$vote->gid)->update([
                        'manage_id'=>$vote->initiate_id,
                        'manager'=>$vote->initiate
                    ]);
                    GroupEvent::groupEventAdd($vote->gid,$vote->initiate_id,$vote->initiate,'弹劾组长成功。');
                }
            }elseif($oppose/$party>0.5 && $vote->status==0){
                GroupVote::where('id',$vote->id)->update([
                    'status'=>2,
                    'remark'=>'未通过。'
                ]);
            }elseif($neutral/$party>0.5 && $vote->status==0){
                GroupVote::where('id',$vote->id)->update([
                    'status'=>2,
                    'remark'=>'未通过。'
                ]);
            }
        // 最后，更新一下投票中的过期记录，这个，这个。。。先这样放着
        $GroupVote = GroupVote::where([['gid',$vote->gid],['status',0]])->get();
        foreach($GroupVote as $value){
            if(Carbon::now()>$value->deadline){
                GroupVote::where('id',$value->id)->update([
                    'status'=>2,
                    'remark'=>'过期。'
                ]);
            }
        }
    }
}
