<?php

namespace App\Home\Encyclopedia\EntryCooperation;

use Illuminate\Database\Eloquent\Model;
use App\Events\Encyclopedia\EntryCooperation\EntryCooperationMessageLeftEvent;
use App\Events\Encyclopedia\EntryCooperation\EntryCooperationMessageRepliedEvent;

class EntryCooperationMessage extends Model
{
    protected $fillable = ['cooperation_id','pid','title','content','author_id','author'];

    // 关联user获得author
    public function getAuthor(){
        return $this->belongsTo('App\Models\User','author_id','id');
    }

    // 得到对应的协作计划
    public function getCooperation(){
        return $this->belongsTo('App\Home\Encyclopedia\EntryCooperation','cooperation_id','id');
    }
    // 得到对应的回复
    public function reply(){
        return $this->hasMany('App\Home\Encyclopedia\EntryCooperation\EntryCooperationMessage','pid','id');
    }

    // 添加留言信息
    protected function MessageAdd($cooperation_id,$title,$message,$author_id,$author){
    	$result = EntryCooperationMessage::create([
            'cooperation_id'  => $cooperation_id,
            'title'     => $title,
            'content'   => $message,
            'author_id' => $author_id,
            'author'     => $author
        ]);
        if($result->id){
        	event(new EntryCooperationMessageLeftEvent($result));
        }
        return $result->id ? '1':'0';
    }

    // 添加留言信息
    protected function MessageReply($cooperation_id,$pid,$title,$message,$author_id,$author){
    	$result = EntryCooperationMessage::create([
            'cooperation_id'  => $cooperation_id,
            'pid'  		=> $pid,
            'title'     => $title,
            'content'   => $message,
            'author_id' => $author_id,
            'author'     => $author
        ]);
        if($result->id){
        	event(new EntryCooperationMessageRepliedEvent($result));
        }
        return $result->id ? '1':'0';
    }
    
}
