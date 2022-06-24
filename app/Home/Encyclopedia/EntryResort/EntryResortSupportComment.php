<?php

namespace App\Home\Encyclopedia\EntryResort;
use App\Events\Encyclopedia\EntryResort\EntryResortSupportCommentCreatedEvent;

use Illuminate\Database\Eloquent\Model;

class EntryResortSupportComment extends Model
{
    protected $fillable = ['eid','resortId','comment','pid','author_id','author','title','type'];

    // 关联user获得author
    public function getAuthor(){
        return $this->belongsTo('App\Models\User','author_id','id');
    }

    // 关联resort表获得resort
    public function getResort(){
        return $this->belongsTo('App\Home\Encyclopedia\EntryResort','resortId','id');
    }
    
    //写入新建的帮助评论内容
    protected function commentAdd($eid,$resortId,$comment,$pid,$title,$author_id,$author){
    	$result = EntryResortSupportComment::create([
    		'eid'	=> $eid,
    		'resortId'	=> $resortId,  		
    		'comment'	=> $comment,
    		'pid'		=> $pid,
            'title'     => $title,
            'author_id' => $author_id,
            'author'	=> $author,
    	]);
        event(new EntryResortSupportCommentCreatedEvent($result));
    	return $result ? '1':'0';
    }

    //求助者拒绝帮助的评论内容
    protected function rejectCommentAdd($eid,$resortId,$comment,$pid,$title,$author_id,$author,$type){
    	$commentArray = array(
    		'eid'	=> $eid,
    		'resortId'	=> $resortId,  		
    		'comment'	=> $comment,
    		'pid'		=> $pid,
            'title'     => $title,
            'author_id' => $author_id,
            'author'	=> $author,
            'type'	=> $type,
    		);
        // 由于拒绝更改状态时已经出发了拒绝事件，因此此处不再event
    	$supportComment = new EntryResortSupportComment;
    	$result = $supportComment -> fill($commentArray) -> save();
    	return $result ? '1':'0';
    }

    //处理评论的无限分级
    protected function commentChild(){
        return $this-> hasMany('App\Home\Encyclopedia\EntryResort\EntryResortSupportComment','pid','id');
    }

    public function allComment() {
        return $this->commentChild()->with('allComment');
    }
}
