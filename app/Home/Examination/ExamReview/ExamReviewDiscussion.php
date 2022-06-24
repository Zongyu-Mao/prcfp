<?php

namespace App\Home\Examination\ExamReview;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\ExamReview\ExamReviewDiscussionCreatedEvent;
use App\Events\Examination\ExamReview\ExamReviewDiscussionRepliedEvent;

class ExamReviewDiscussion extends Model
{
    public $timestamps = true;
    protected $fillable = ['rid','author_id','author','title','comment','pid','standpoint'];

    // 关联user获得author
    public function getAuthor(){
        return $this->belongsTo('App\Models\User','author_id','id');
    }

    // 关联review表获得review
    public function getReview(){
        return $this->belongsTo('App\Home\Examination\ExamReview','rid','id');
    }

    //模型处理评审意见（中立和支持）的添加
    protected function reviewCommentAdd($reviewId,$author_id,$author,$title,$comment,$pid,$standpoint){
    	$discussionArray = array(
    		'rid'		=> $reviewId,
            'author_id' => $author_id,
    		'author'	=> $author,
    		'title'     => $title,
    		'comment'	=> $comment,
    		'pid'		=> $pid,
            'standpoint'=> $standpoint,
    		);
    	$reviewDiscussion = new ExamReviewDiscussion;
    	$result = $reviewDiscussion -> fill($discussionArray) -> save();
        if($reviewDiscussion->id && $pid == '0'){
            event(new ExamReviewDiscussionCreatedEvent($reviewDiscussion));
        }elseif($reviewDiscussion->id && $pid != '0'){
            event(new ExamReviewDiscussionRepliedEvent($reviewDiscussion));
        }
    	return $result ? '1':'0';
    }

    //处理讨论区的回复（这个回复是所有人都可以操作的，不限于协作小组，是无限级嵌套）
    protected function discussChild(){
    	return $this-> hasMany('App\Home\Examination\ExamReview\ExamReviewDiscussion','pid','id');
    }
    
    public function allDiscuss() {
    	return $this->discussChild()->with('allDiscuss');
    }
}
