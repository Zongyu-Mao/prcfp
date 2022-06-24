<?php

namespace App\Home\Personnel\Inform;

use Illuminate\Database\Eloquent\Model;

class InformEvent extends Model
{
    //
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
    	$informEvent = new InformEvent;
    	$result = $informEvent -> fill($eventArray) -> save();
    	return $result;
    }
}
