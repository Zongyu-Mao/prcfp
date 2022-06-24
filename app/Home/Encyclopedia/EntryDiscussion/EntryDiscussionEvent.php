<?php

namespace App\Home\Encyclopedia\EntryDiscussion;

use Illuminate\Database\Eloquent\Model;

class EntryDiscussionEvent extends Model
{
    protected $fillable = ['eid','user_id','username','content'];

    //模型处理评审事件的添加
    protected function discussionEventAdd($eid,$user_id,$username,$content){
    	$eventArray = array(
    		'eid'	=> $eid,
    		'user_id'	=> $user_id,
    		'username'	=> $username,
    		'content'	=> $content,
    		);
    	$entryDiscussionEvent = new EntryDiscussionEvent;
    	$result = $entryDiscussionEvent -> fill($eventArray) -> save();
    	return $result ? '1':'0';
    }
}
