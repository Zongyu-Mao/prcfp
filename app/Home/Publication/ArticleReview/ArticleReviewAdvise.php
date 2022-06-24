<?php

namespace App\Home\Publication\ArticleReview;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\ArticleReview\ArticleReviewAdvisementCreatedEvent;
use App\Events\Publication\ArticleReview\ArticleReviewAdvisementAcceptedEvent;
use App\Events\Publication\ArticleReview\ArticleReviewAdvisementRejectedEvent;

class ArticleReviewAdvise extends Model
{
    protected $fillable = ['rid','title','comment','pid','author_id','author','recipient_id','recipient','round','status'];

    // 关联user获得author
    public function getAuthor(){
        return $this->belongsTo('App\Models\User','author_id','id');
    }

    // 关联review表获得review
    public function getReview(){
        return $this->belongsTo('App\Home\Publication\ArticleReview','rid','id');
    }

    // 评审计划建议评论的创建
    protected function reviewAdvisementCreate($rid,$title,$comment,$author_id,$author){
     	 $result = ArticleReviewAdvise::create([
            'rid' 		=> $rid,
            'title'     => $title,
            'comment'   => $comment,
            'author_id' => $author_id,
            'author'   	=> $author,
        ]);
     	 if($result->id){
     	 	event(new ArticleReviewAdvisementCreatedEvent($result));
     	 }
     	 return $result->id ? '1':'0';
     }

     // 评审计划建议评论的接受
    protected function reviewAdvisementAccept($id,$recipient_id,$recipient,$status){
         $result = ArticleReviewAdvise::where('id',$id)->update([
                'recipient_id'  => $recipient_id,
                'recipient'     => $recipient,
                'status'        => $status,
            ]);
         if($result){
            event(new ArticleReviewAdvisementAcceptedEvent(ArticleReviewAdvise::find($id)));
         }
         return $result ? '1':'0';
     }

     // 评审计划建议评论的拒绝
    protected function reviewAdvisementReject($rid,$title,$comment,$pid,$author_id,$author,$recipient_id,$recipient){
         $result = ArticleReviewAdvise::create([
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
            ArticleReviewAdvise::where('id',$pid)->update([
                    'recipient_id'  => $author_id,
                    'recipient'     => $author,
                    'status'        => '2',
                ]);
            event(new ArticleReviewAdvisementRejectedEvent($result));
         }
         return $result->id ? '1':'0';
     }



    protected function adviseChild(){
    	return $this-> hasMany('App\Home\Publication\ArticleReview\ArticleReviewAdvise','pid','id');
    }
	public function allAdvise() {
    	return $this->adviseChild()->with('allAdvise');
    }
}
