<?php

namespace App\Home\Publication\ArticleDebate;

use Illuminate\Database\Eloquent\Model;

class ArticleDebateEvent extends Model
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
    	$debateEvent = new ArticleDebateEvent;
    	$result = $debateEvent -> fill($eventArray) -> save();
    	return $result ? '1':'0';
    }
}
