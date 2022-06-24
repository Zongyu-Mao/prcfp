<?php

namespace App\Home\Examination\ExamResort;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\ExamResort\ExamResortSupportCommentCreatedEvent;

class ExamResortSupportComment extends Model
{
    protected $fillable = ['exam_id','resortId','comment','pid','author_id','title','type'];

    // 关联user获得author
    public function getAuthor(){
        return $this->belongsTo('App\Models\User','author_id','id');
    }

    // 关联review表获得review
    public function getResort(){
        return $this->belongsTo('App\Home\Examination\ExamResort','resortId','id');
    }
    
    //写入新建的帮助评论内容
    protected function commentAdd($exam_id,$resortId,$comment,$pid,$title,$author_id){
    	$result = ExamResortSupportComment::create([
    		'exam_id'	=> $exam_id,
    		'resortId'	=> $resortId,  		
    		'comment'	=> $comment,
    		'pid'		=> $pid,
            'title'     => $title,
            'author_id'	=> $author_id
    	]);
    	event(new ExamResortSupportCommentCreatedEvent($result));
    	return $result ? '1':'0';
    }

    //求助者拒绝帮助的评论内容
    protected function rejectCommentAdd($exam_id,$resortId,$comment,$pid,$title,$author_id,$type){
    	$commentArray = array(
    		'exam_id'	=> $exam_id,
    		'resortId'	=> $resortId,  		
    		'comment'	=> $comment,
    		'pid'		=> $pid,
            'title'     => $title,
            'author_id'	=> $author_id,
            'type'	=> $type,
    		);
        // 触发拒绝和接受帮助的事件在resort中
    	$supportComment = new ExamResortSupportComment;
    	$result = $supportComment -> fill($commentArray) -> save();
    	return $result ? '1':'0';
    }

    //处理评论的无限分级
    protected function commentChild(){
        return $this-> hasMany('App\Home\Examination\ExamResort\ExamResortSupportComment','pid','id');
    }

    public function allComment() {
        return $this->commentChild()->with('allComment');
    }
}
