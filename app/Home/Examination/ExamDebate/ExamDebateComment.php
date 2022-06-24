<?php

namespace App\Home\Examination\ExamDebate;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\ExamDebate\ExamDebateComment\ExamDebateCommentCreatedEvent;

class ExamDebateComment extends Model
{
    protected $fillable = ['exam_id','debate_id','comment','pid','author_id','title','up','down','type'];

    // 关联user获得author
    public function getAuthor(){
        return $this->belongsTo('App\Models\User','author_id','id');
    }

    // 关联review表获得review
    public function getDebate(){
        return $this->belongsTo('App\Home\Examination\ExamDebate','debate_id','id');
    }

    //评论内容的添加
    protected function commentAdd($exam_id,$debate_id,$comment,$pid,$author_id,$title,$type){
    	$result = ExamDebateComment::create([
    		'exam_id'	=> $exam_id,
    		'debate_id'	=> $debate_id,
    		'comment'	=> $comment,
    		'pid'		=> $pid,
    		'author_id'	=> $author_id,
            'title'     => $title,
    		'type'		=> $type,
    	]);
    	event(new ExamDebateCommentCreatedEvent($result));
    	return $result ? '1':'0';
    }

    //评论内容的获取
    protected function commentChild(){
        return $this-> hasMany('App\Home\Examination\ExamDebate\ExamDebateComment','pid','id');
    }

    public function allComment() {
        return $this->commentChild()->with('allComment');
    }
}
