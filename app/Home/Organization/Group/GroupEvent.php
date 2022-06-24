<?php

namespace App\Home\Organization\Group;

use Illuminate\Database\Eloquent\Model;

class GroupEvent extends Model
{
    protected $fillable = ['gid','user_id','username','content'];

    //新增协作动态的函数
    protected function groupEventAdd($gid,$userid,$username,$content){
    	$eventArray = array(
    		'gid'	=> $gid,
            'user_id'   => $userid,
    		'username'	=> $username,
    		'content'	=> $content,
    		);
    	$groupEvent = new GroupEvent;
    	$result = $groupEvent -> fill($eventArray) -> save();
    	return $result;
    }
}
