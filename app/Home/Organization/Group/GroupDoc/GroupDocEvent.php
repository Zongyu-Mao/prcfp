<?php

namespace App\Home\Organization\Group\GroupDoc;

use Illuminate\Database\Eloquent\Model;

class GroupDocEvent extends Model
{
	public $timestamps = false;
    protected $fillable = ['did','user_id','username','content','createtime'];

    //模型处理评审事件的添加
    protected function groupDocEventAdd($did,$user_id,$username,$content,$createtime){
    	$eventArray = array(
    		'did'		=> $did,
    		'user_id'	=> $user_id,
    		'username'	=> $username,
            'content'   => $content,
    		'createtime'	=> $createtime,
    		);
    	$event = new GroupDocEvent;
    	$result = $event -> fill($eventArray) -> save();
    	return $result ? '1':'0';
    }
}
