<?php

namespace App\Home\Encyclopedia\EntryDebate;

use Illuminate\Database\Eloquent\Model;

class EntryDebateEvent extends Model
{
    protected $fillable = ['debate_id','user_id','username','content'];

    //模型处理评审事件的添加
    protected function debateEventAdd($debate_id,$user_id,$username,$content){
    	$eventArray = array(
    		'debate_id'	=> $debate_id,
    		'user_id'	=> $user_id,
    		'username'	=> $username,
    		'content'	=> $content,
    		);
    	$entryDebateEvent = new EntryDebateEvent;
    	$result = $entryDebateEvent -> fill($eventArray) -> save();
    	return $result ? '1':'0';
    }
}
