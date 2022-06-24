<?php

namespace App\Home\Examination\ExamReview;

use Illuminate\Database\Eloquent\Model;

class ExamReviewEvent extends Model
{
    protected $fillable = ['rid','user_id','username','content'];

    //模型处理评审事件的添加
    protected function reviewEventAdd($reviewId,$user_id,$username,$content){
    	$eventArray = array(
    		'rid'		=> $reviewId,
    		'user_id'	=> $user_id,
    		'username'	=> $username,
    		'content'	=> $content,
    		);
    	$reviewEvent = new ExamReviewEvent;
    	$result = $reviewEvent -> fill($eventArray) -> save();
    	return $result ? '1':'0';
    }
}
