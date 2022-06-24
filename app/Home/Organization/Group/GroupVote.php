<?php

namespace App\Home\Organization\Group;

use Illuminate\Database\Eloquent\Model;
use App\Events\Organization\Group\Vote\VoteCreatedEvent;

class GroupVote extends Model
{
    protected $fillable = ['gid','type','deadline','initiate_id','initiate','title','content','status','remark'];

    // 一对一关联发起者信息
    public function getCreator(){
        return $this->hasOne('App\Models\User','id','initiate_id');
    }

    // 一对多关联投票记录
    public function getVoteRecord(){
        return $this->hasMany('App\Home\Organization\Group\GroupVoteRecord','vote_id','id');
    }



    // 处理协作计划的投票创建,投票仅存在type的不同，所以可以用一个函数全部处理
    protected function groupVote($gid,$type,$deadline,$initiate_id,$initiate,$title,$content){
        $voteArr = array(
            'gid'  	=> $gid,
            'type'      => $type,
            'deadline'  => $deadline,
            'initiate_id' => $initiate_id,
            'initiate' => $initiate,
            'title'     => $title,
            'content'   => $content,
            );
        $groupVote = new GroupVote;
        $result = $groupVote -> fill($voteArr) -> save();
        if($groupVote->id){
            event(new VoteCreatedEvent($groupVote));
        }
        return $groupVote->id ? '1':'0';
    }
}
