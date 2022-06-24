<?php

namespace App\Home\Encyclopedia\EntryReview;

use Illuminate\Database\Eloquent\Model;
use App\Events\Encyclopedia\EntryReview\EntryReviewAdvisementCreatedEvent;
use App\Events\Encyclopedia\EntryReview\EntryReviewAdvisementAcceptedEvent;
use App\Events\Encyclopedia\EntryReview\EntryReviewAdvisementRejectedEvent;

class EntryReviewAdvise extends Model
{
    protected $fillable = ['rid','title','comment','pid','author_id','author','recipient_id','recipient','round','status'];

    // 关联user获得author
    public function getAuthor(){
        return $this->belongsTo('App\Models\User','author_id','id');
    }

    // 关联review表获得review
    public function getReview(){
        return $this->belongsTo('App\Home\Encyclopedia\EntryReview','rid','id');
    }

    // 评审计划建议评论的创建
    protected function reviewAdvisementCreate($rid,$title,$comment,$author_id,$author){
     	 $result = EntryReviewAdvise::create([
            'rid' 		=> $rid,
            'title'     => $title,
            'comment'   => $comment,
            'author_id' => $author_id,
            'author'   	=> $author,
        ]);
     	 if($result->id){
     	 	event(new EntryReviewAdvisementCreatedEvent($result));
     	 }
     	 return $result->id ? '1':'0';
     }

     // 评审计划建议评论的接受
    protected function reviewAdvisementAccept($id,$recipient_id,$recipient,$status){
         $result = EntryReviewAdvise::where('id',$id)->update([
                'recipient_id'  => $recipient_id,
                'recipient'     => $recipient,
                'status'        => $status,
            ]);
         if($result){
            event(new EntryReviewAdvisementAcceptedEvent(EntryReviewAdvise::find($id)));
         }
         return $result ? '1':'0';
     }

     // 评审计划建议评论的拒绝
    protected function reviewAdvisementReject($rid,$title,$comment,$pid,$author_id,$author,$recipient_id,$recipient){
         $result = EntryReviewAdvise::create([
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
            EntryReviewAdvise::where('id',$pid)->update([
                    'recipient_id'  => $author_id,
                    'recipient'     => $author,
                    'status'        => '2',
                ]);
            event(new EntryReviewAdvisementRejectedEvent($result));
         }
         return $result->id ? '1':'0';
     }



    protected function adviseChild(){
    	return $this-> hasMany('App\Home\Encyclopedia\EntryReview\EntryReviewAdvise','pid','id');
    }
	public function allAdvise() {
    	return $this->adviseChild()->with('allAdvise');
    }

}
