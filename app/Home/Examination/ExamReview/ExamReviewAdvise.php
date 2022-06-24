<?php

namespace App\Home\Examination\ExamReview;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\ExamReview\ExamReviewAdvisementCreatedEvent;
use App\Events\Examination\ExamReview\ExamReviewAdvisementAcceptedEvent;
use App\Events\Examination\ExamReview\ExamReviewAdvisementRejectedEvent;

class ExamReviewAdvise extends Model
{
    protected $fillable = ['rid','title','comment','pid','author_id','author','recipient_id','recipient','round','status'];

    // 关联user获得author
    public function getAuthor(){
        return $this->belongsTo('App\Models\User','author_id','id');
    }

    // 关联review表获得review
    public function getReview(){
        return $this->belongsTo('App\Home\Examination\ExamReview','rid','id');
    }

    // 评审计划建议评论的创建
    protected function reviewAdvisementCreate($rid,$title,$comment,$author_id,$author){
     	 $result = ExamReviewAdvise::create([
            'rid' 		=> $rid,
            'title'     => $title,
            'comment'   => $comment,
            'author_id' => $author_id,
            'author'   	=> $author,
        ]);
     	 if($result->id){
     	 	event(new ExamReviewAdvisementCreatedEvent($result));
     	 }
     	 return $result->id ? '1':'0';
     }

     // 评审计划建议评论的接受
    protected function reviewAdvisementAccept($id,$recipient_id,$recipient,$status){
         $result = ExamReviewAdvise::where('id',$id)->update([
                'recipient_id'  => $recipient_id,
                'recipient'     => $recipient,
                'status'        => $status,
            ]);
         if($result){
            event(new ExamReviewAdvisementAcceptedEvent(ExamReviewAdvise::find($id)));
         }
         return $result ? '1':'0';
     }

     // 评审计划建议评论的拒绝
    protected function reviewAdvisementReject($rid,$title,$comment,$pid,$author_id,$author,$recipient_id,$recipient){
         $result = ExamReviewAdvise::create([
            'rid'       => $rid,
            'title'     => $title,
            'comment'   => $comment,
            'pid'       => $pid,
            'author_id' => $author_id,
            'author'    => $author,
            'recipient_id'  => $recipient_id,
            'recipient'     => $recipient,
        ]);
         if($result->id){
            ExamReviewAdvise::where('id',$pid)->update([
                    'recipient_id'  => $author_id,
                    'recipient'     => $author,
                    'status'        => '2',
                ]);
            event(new ExamReviewAdvisementRejectedEvent($result));
         }
         return $result->id ? '1':'0';
     }



    protected function adviseChild(){
    	return $this-> hasMany('App\Home\Examination\ExamReview\ExamReviewAdvise','pid','id');
    }
	public function allAdvise() {
    	return $this->adviseChild()->with('allAdvise');
    }
}
