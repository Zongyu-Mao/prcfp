<?php

namespace App\Home\Personnel\JudgementInform;

use Illuminate\Database\Eloquent\Model;

class JudgementInformEvent extends Model
{
    protected $fillable = ['inform_id','user_id','username','content','createtime'];
    public $timestamps = false;
    // 一对一关联组长信息

    //新增协作动态的函数
    protected function informEventAdd($inform_id,$userid,$content,$createtime){
    	$eventArray = array(
    		'inform_id'	=> $inform_id,
            'user_id'   => $userid,
    		'content'	=> $content,
    		'createtime'	=> $createtime,
    		);
    	$informEvent = new JudgementInformEvent;
    	$result = $informEvent -> fill($eventArray) -> save();
    	return $result;
    }
}
