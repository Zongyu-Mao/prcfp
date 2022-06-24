<?php

namespace App\Home\Examination;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\ExamDiscussion\ExamDiscussionCreatedEvent;
use App\Events\Examination\ExamDiscussion\ExamDiscussionRepliedEvent;

class ExamDiscussion extends Model
{
    protected $fillable = ['exam_id','title','comment','pid','author_id','author','recipient_id','recipient','stars','round','status'];

    //一对一关联，获得试卷信息
    public function getExam(){
        return $this->belongsTo('App\Home\Examination\Exam','exam_id','id');
    }

    //写入词条讨论信息
     protected function discussionAdd($exam_id,$title,$comment,$pid,$author_id,$author){
    	$discussionArray = array(
    		'exam_id'	=> $exam_id,
    		'title'		=> $title,
    		'comment'	=> $comment,
    		'pid'		=> $pid,
    		'author_id'	=> $author_id,
    		'author'	=> $author,
    		);
    	$discussion = new ExamDiscussion;
    	$result = $discussion -> fill($discussionArray) -> save();
        if($discussion->id && $pid == '0'){
            event(new ExamDiscussionCreatedEvent($discussion));
        }elseif($discussion->id && $pid != '0'){
            event(new ExamDiscussionRepliedEvent($discussion));
        }
    	return $result ? '1':'0';
    }

    //处理讨论区的显示
    protected function discussChild(){
        return $this-> hasMany('App\Home\Examination\ExamDiscussion','pid','id');
    }

    public function allDiscuss() {
        return $this->discussChild()->with('allDiscuss');
    }
}
