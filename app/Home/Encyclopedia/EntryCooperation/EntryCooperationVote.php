<?php

namespace App\Home\Encyclopedia\EntryCooperation;

use Illuminate\Database\Eloquent\Model;
use App\Events\Encyclopedia\EntryCooperation\EntryCooperationVoteCreatedEvent;

class EntryCooperationVote extends Model
{

    protected $fillable = ['cooperation_id','eid','type','deadline','initiate_id','initiate','title','content','status','remark'];

    // 一对一关联发起者信息
    public function getCreator(){
        return $this->hasOne('App\Models\User','id','initiate_id');
    }

    // 一对多关联投票记录
    public function getVoteRecord(){
        return $this->hasMany('App\Home\Encyclopedia\EntryCooperation\EntryCooperationVoteRecord','vote_id','id');
    }

    // 一对多关联投票记录拿到user_id的数组
    public function getVoteRecordArray(){
        return $this->hasMany('App\Home\Encyclopedia\EntryCooperation\EntryCooperationVoteRecord','vote_id','id')->pluck('user_id')->toArray();
    }



    // 处理协作计划的投票创建,投票仅存在type的不同，所以可以用一个函数全部处理
    protected function cooperationVote($cooperation_id,$eid,$type,$deadline,$initiate_id,$initiate,$title,$content){
        $voteArr = array(
            'cooperation_id'  => $cooperation_id,
            'eid'    => $eid,
            'type'      => $type,
            'deadline'  => $deadline,
            'initiate_id' => $initiate_id,
            'initiate' => $initiate,
            'title'     => $title,
            'content'   => $content,
            );
        $EntryCooperationVote = new EntryCooperationVote;
        $result = $EntryCooperationVote -> fill($voteArr) -> save();
        if($EntryCooperationVote->id){
            event(new EntryCooperationVoteCreatedEvent($EntryCooperationVote));
        }
        return $EntryCooperationVote->id ? '1':'0';
    }


}
