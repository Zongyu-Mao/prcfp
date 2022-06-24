<?php

namespace App\Home\Examination\ExamCooperation;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\ExamCooperation\Discussion\MessageLeftEvent;
use App\Events\Examination\ExamCooperation\Discussion\MessageRepliedEvent;

class ExamCooperationMessage extends Model
{
    protected $fillable = ['cooperation_id','pid','title','content','author_id','author'];

    // 关联user获得author
    public function getAuthor(){
        return $this->belongsTo('App\User','author_id','id');
    }

    // 得到对应的协作计划
    public function getCooperation(){
        return $this->belongsTo('App\Home\Examination\ExamCooperation','cooperation_id','id');
    }
    // 得到对应的回复
    public function reply(){
        return $this->hasMany('App\Home\Examination\ExamCooperation\ExamCooperationMessage','pid','id');
    }

    // 添加留言信息
    protected function MessageAdd($cooperation_id,$title,$message,$author_id,$author){
    	$result = ExamCooperationMessage::create([
            'cooperation_id'  => $cooperation_id,
            'title'     => $title,
            'content'   => $message,
            'author_id' => $author_id,
            'author'    => $author
        ]);
        if($result->id){
        	event(new MessageLeftEvent($result));
        }
        return $result->id ? '1':'0';
    }

    // 添加留言信息
    protected function MessageReply($cooperation_id,$pid,$title,$message,$author_id,$author){
    	$result = ExamCooperationMessage::create([
            'cooperation_id'  => $cooperation_id,
            'pid'  		=> $pid,
            'title'     => $title,
            'content'   => $message,
            'author_id' => $author_id,
            'author'    => $author
        ]);
        if($result->id){
        	event(new MessageRepliedEvent($result));
        }
        return $result->id ? '1':'0';
    }
}
