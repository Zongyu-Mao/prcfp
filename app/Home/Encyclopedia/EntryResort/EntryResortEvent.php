<?php

namespace App\Home\Encyclopedia\EntryResort;

use Illuminate\Database\Eloquent\Model;

class EntryResortEvent extends Model
{
    protected $fillable = ['eid','user_id','username','content'];

    //模型处理求助事件的添加
    protected function resortEventAdd($eid,$user_id,$username,$content){
    	$eventArray = array(
    		'eid'	=> $eid,
    		'user_id'	=> $user_id,
    		'username'	=> $username,
    		'content'	=> $content,
    		);
    	$resortEvent = new EntryResortEvent;
    	$result = $resortEvent -> fill($eventArray) -> save();
    	return $result ? '1':'0';
    }
}
