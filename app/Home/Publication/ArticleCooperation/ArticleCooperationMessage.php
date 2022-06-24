<?php

namespace App\Home\Publication\ArticleCooperation;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\ArticleCooperation\ArticleCooperationMessageLeftEvent;
use App\Events\Publication\ArticleCooperation\ArticleCooperationMessageRepliedEvent;

class ArticleCooperationMessage extends Model
{
    protected $fillable = ['cooperation_id','pid','title','content','author_id','author'];

    // 关联user获得author
    public function getAuthor(){
        return $this->belongsTo('App\Models\User','author_id','id');
    }

    // 得到对应的协作计划
    public function getCooperation(){
        return $this->belongsTo('App\Home\Publication\ArticleCooperation','cooperation_id','id');
    }
    // 得到对应的回复
    public function reply(){
        return $this->hasMany('App\Home\Publication\ArticleCooperation\ArticleCooperationMessage','pid','id');
    }

    // 添加留言信息
    protected function MessageAdd($cooperation_id,$title,$message,$author_id,$author){
    	$result = ArticleCooperationMessage::create([
            'cooperation_id'  => $cooperation_id,
            'title'     => $title,
            'content'   => $message,
            'author_id' => $author_id,
            'author'    => $author
        ]);
        if($result->id){
        	event(new ArticleCooperationMessageLeftEvent($result));
        }
        return $result->id ? '1':'0';
    }

    // 添加留言信息
    protected function MessageReply($cooperation_id,$pid,$title,$message,$author_id,$author){
    	$result = ArticleCooperationMessage::create([
            'cooperation_id'  => $cooperation_id,
            'pid'  		=> $pid,
            'title'     => $title,
            'content'   => $message,
            'author_id' => $author_id,
            'author'    => $author
        ]);
        if($result->id){
        	event(new ArticleCooperationMessageRepliedEvent($result));
        }
        return $result->id ? '1':'0';
    }
}
