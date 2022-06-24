<?php

namespace App\Listeners\Organization\Group\Vote;

use App\Events\Organization\Group\Vote\VoteCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Home\Organization\Group\GroupEvent;
use App\Models\User;

class VoteCreatedListener
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
     * @param  VoteCreatedEvent  $event
     * @return void
     */
    public function handle(VoteCreatedEvent $event)
    {
        //投票创建成功后，加入协作事件。
        //判断投票类型，1自定义协作事务2申请进组3弹劾组长4劝退组员
        $vote = $event->groupVote;
        if($vote->type==1){
            $voteEvent='发起组织事务投票：';
            }elseif($vote->type==2){
                $voteEvent='申请进入组织：';
            }elseif($vote->type==3){
                $voteEvent='发起的组长弹劾：';
            }elseif($vote->type==4){
                $voteEvent='发起的组员劝退：';
            }
        //发表了有效的申请后，积分和成长值+5
        User::expAndGrowValue($vote->initiate_id,5,5);
        //本次申请计入协作事件
        // GroupDynamic::dynamicAdd($gid,$gTitle,$behavior,$objectName,$objectURL,$createtime)
        GroupEvent::groupEventAdd($vote->gid,$vote->initiate_id,$vote->initiate,$voteEvent.$vote->title.'。');
    }
}
