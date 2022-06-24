<?php

namespace App\Home\Examination\ExamDebate;

use Illuminate\Database\Eloquent\Model;

class ExamDebateEvent extends Model
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
    	$debateEvent = new ExamDebateEvent;
    	$result = $debateEvent -> fill($eventArray) -> save();
    	return $result ? '1':'0';
    }
}
