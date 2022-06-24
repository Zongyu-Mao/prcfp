<?php

namespace App\Home\Encyclopedia\EntryCooperation;

use Illuminate\Database\Eloquent\Model;

class EntryCooperationEvent extends Model
{
    //
    protected $fillable = ['cooperation_id','user_id','username','content'];
    // 一对一关联组长信息

    //新增协作动态的函数
    protected function cooperationEventAdd($cooperation_id,$userid,$username,$content){
    	$eventArray = array(
    		'cooperation_id'	=> $cooperation_id,
            'user_id'   => $userid,
    		'username'	=> $username,
    		'content'	=> $content,
    		);
    	$cooperationEvent = new EntryCooperationEvent;
    	$result = $cooperationEvent -> fill($eventArray) -> save();
    	return $result;
    }
}
