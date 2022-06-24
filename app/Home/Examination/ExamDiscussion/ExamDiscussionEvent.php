<?php

namespace App\Home\Examination\ExamDiscussion;

use Illuminate\Database\Eloquent\Model;

class ExamDiscussionEvent extends Model
{
    protected $fillable = ['exam_id','user_id','username','content'];

    //模型处理评审事件的添加
    protected function discussionEventAdd($exam_id,$user_id,$username,$content){
    	$eventArray = array(
    		'exam_id'	=> $exam_id,
    		'user_id'	=> $user_id,
    		'username'	=> $username,
    		'content'	=> $content,
    		);
    	$event = new ExamDiscussionEvent;
    	$result = $event -> fill($eventArray) -> save();
    	return $result ? '1':'0';
    }
}
