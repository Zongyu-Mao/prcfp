<?php

namespace App\Home\Encyclopedia\EntryDebate;

use Illuminate\Database\Eloquent\Model;
use App\Events\Encyclopedia\EntryDebate\EntryDebateComment\EntryDebateCommentCreatedEvent;

class EntryDebateComment extends Model
{
    protected $fillable = ['eid','debate_id','comment','pid','author_id','author','title','up','down','type'];

    // 关联user获得author
    public function getAuthor(){
        return $this->belongsTo('App\Models\User','author_id','id');
    }

    // 关联review表获得review
    public function getDebate(){
        return $this->belongsTo('App\Home\Encyclopedia\EntryDebate','debate_id','id');
    }

    //评论内容的添加
    protected function commentAdd($eid,$debate_id,$comment,$pid,$author_id,$author,$title,$type){
    	$result = EntryDebateComment::create([
    		'eid'		=> $eid,
    		'debate_id'	=> $debate_id,
    		'comment'	=> $comment,
    		'pid'		=> $pid,
            'author_id' => $author_id,
    		'author'	=> $author,
            'title'     => $title,
    		'type'		=> $type,
    	]);
        event(new EntryDebateCommentCreatedEvent($result));
    	return $result ? '1':'0';
    }

    //评论内容的获取
    protected function commentChild(){
        return $this-> hasMany('App\Home\Encyclopedia\EntryDebate\EntryDebateComment','pid','id');
    }

    public function allComment() {
        return $this->commentChild()->with('allComment');
    }
}
