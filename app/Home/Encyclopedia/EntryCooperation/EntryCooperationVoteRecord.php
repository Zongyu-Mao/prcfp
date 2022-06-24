<?php

namespace App\Home\Encyclopedia\EntryCooperation;

use Illuminate\Database\Eloquent\Model;
use App\Events\Encyclopedia\EntryCooperation\EntryCooperationVoteRecordCreatedEvent;

class EntryCooperationVoteRecord extends Model
{
    public $timestamps = false;
    //定义时间戳模型
    //protected $dateFormat = 'U';

    protected $fillable = ['vote_id','user_id','username','standpoint','createtime'];

    // 写入投票记录
    protected function voteAdd($voteId,$userId,$username,$standpoint,$createtime){
    	$result = EntryCooperationVoteRecord::create([
    		'vote_id'	=>$voteId,
           'user_id'   =>$userId,
    		'username'	=>$username,
    		'standpoint'=>$standpoint,
    		'createtime'=>$createtime
    	]);
       if($result->id){
           event(new EntryCooperationVoteRecordCreatedEvent($result));
       }
    	return $result->id ? '1':'0';
    }
    // 获取同意的票数
    protected function getAgreeNum($id){
    	return $result = EntryCooperationVoteRecord::where([['vote_id',$id],['standpoint',1]])->count();
    }
    // 获取反对的票数
    protected function getOpposeNum($id){
    	return $result = EntryCooperationVoteRecord::where([['vote_id',$id],['standpoint',2]])->count();
    }
    // 获取中立的票数
    protected function getNeutralNum($id){
    	return $result = EntryCooperationVoteRecord::where([['vote_id',$id],['standpoint',3]])->count();
    }

}
