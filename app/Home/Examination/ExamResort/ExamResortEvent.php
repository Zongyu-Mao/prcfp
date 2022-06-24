<?php

namespace App\Home\Examination\ExamResort;

use Illuminate\Database\Eloquent\Model;

class ExamResortEvent extends Model
{
    protected $fillable = ['exam_id','user_id','username','content'];

    //模型处理求助事件的添加
    protected function resortEventAdd($exam_id,$user_id,$username,$content){
    	$eventArray = array(
    		'exam_id'	=> $exam_id,
    		'user_id'	=> $user_id,
    		'username'	=> $username,
    		'content'	=> $content,
    		);
    	$resortEvent = new ExamResortEvent;
    	$result = $resortEvent -> fill($eventArray) -> save();
    	return $result ? '1':'0';
    }
}
