<?php

namespace App\Home\Examination\ExamCooperation;

use Illuminate\Database\Eloquent\Model;

class ExamCooperationEvent extends Model
{
    protected $fillable = ['cooperation_id','user_id','username','content'];

    //新增协作动态的函数
    protected function cooperationEventAdd($cooperation_id,$userid,$username,$content){
    	$eventArray = array(
    		'cooperation_id'	=> $cooperation_id,
            'user_id'   => $userid,
    		'username'	=> $username,
    		'content'	=> $content,
    		);
    	$cooperationEvent = new ExamCooperationEvent;
    	$result = $cooperationEvent -> fill($eventArray) -> save();
    	return $result;
    }
}
