<?php

namespace App\Home\Publication\ArticleResort;

use Illuminate\Database\Eloquent\Model;

class ArticleResortEvent extends Model
{
    protected $fillable = ['aid','user_id','username','content'];

    //模型处理求助事件的添加
    protected function resortEventAdd($aid,$user_id,$username,$content){
    	$eventArray = array(
    		'aid'	=> $aid,
    		'user_id'	=> $user_id,
    		'username'	=> $username,
    		'content'	=> $content,
    		);
    	$resortEvent = new ArticleResortEvent;
    	$result = $resortEvent -> fill($eventArray) -> save();
    	return $result ? '1':'0';
    }
}
