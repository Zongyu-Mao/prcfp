<?php

namespace App\Home\Publication\ArticleDiscussion;

use Illuminate\Database\Eloquent\Model;

class ArticleDiscussionEvent extends Model
{
    protected $fillable = ['aid','user_id','username','content'];

    //模型处理评审事件的添加
    protected function discussionEventAdd($aid,$user_id,$username,$content){
    	$eventArray = array(
    		'aid'	=> $aid,
    		'user_id'	=> $user_id,
    		'username'	=> $username,
    		'content'	=> $content,
    		);
    	$event = new ArticleDiscussionEvent;
    	$result = $event -> fill($eventArray) -> save();
    	return $result ? '1':'0';
    }
}
