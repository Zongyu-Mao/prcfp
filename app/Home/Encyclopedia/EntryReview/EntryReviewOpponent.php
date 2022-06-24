<?php

namespace App\Home\Encyclopedia\EntryReview;

use Illuminate\Database\Eloquent\Model;
use App\Events\Encyclopedia\EntryReview\EntryReviewOpponentCreatedEvent;
use App\Events\Encyclopedia\EntryReview\EntryReviewOpponentRejectedEvent;
use App\Events\Encyclopedia\EntryReview\EntryReviewOpponentAcceptedEvent;

class EntryReviewOpponent extends Model
{
    protected $fillable = ['rid','title','comment','pid','author_id','author','recipient_id','recipient','stars','status'];

    // 关联user获得author
    public function getAuthor(){
        return $this->belongsTo('App\Models\User','author_id','id');
    }

    // 关联review表获得review
    public function getReview(){
        return $this->belongsTo('App\Home\Encyclopedia\EntryReview','rid','id');
    }

    // 评审反对意见的创建
    protected function opponentAdd($rid,$title,$comment,$author_id,$author){
    	$result = EntryReviewOpponent::create([
                    'rid' 		=> $rid,
                    'title'     => $title,
                    'comment'   => $comment,
                    'author_id' => $author_id,
                    'author'   	=> $author,
                ]);
        if($result->id){
            event(new EntryReviewOpponentCreatedEvent($result));
        }
    	return $result->id ? '1':'0';
    }

    // 评审反对意见的拒绝
    protected function opponentReject($rid,$title,$comment,$pid,$author_id,$author,$recipient_id,$recipient){
    	$result = EntryReviewOpponent::create([
                    'rid' 		=> $rid,
                    'title'     => $title,
                    'comment'   => $comment,
                    'pid'		=> $pid,
                    'author_id' => $author_id,
                    'author'   	=> $author,
                    'recipient_id'	=> $recipient_id,
                	'recipient'		=> $recipient,
                ]);
        if($result->id){
        	EntryReviewOpponent::where('id',$pid)->update([
                	'recipient_id'	=> $author_id,
                	'recipient'		=> $author,
                	'status'		=> '2',
                ]);
            event(new EntryReviewOpponentRejectedEvent($result));
        }
    	return $result->id ? '1':'0';
    }

    // 评审反对意见的接受
    protected function opponentAccept($id,$recipient_id,$recipient,$status){
    	$result = EntryReviewOpponent::where('id',$id)->update([
                	'recipient_id'	=> $recipient_id,
                	'recipient'		=> $recipient,
                	'status'		=> $status,
                ]);
    	if($result){
    		event(new EntryReviewOpponentAcceptedEvent(EntryReviewOpponent::find($id)));
    	}
    	return $result ? '1':'0';
    }


	//处理反对区的回复
    protected function opposeChild(){
    	return $this-> hasMany('App\Home\Encyclopedia\EntryReview\EntryReviewOpponent','pid','id');
    }

    public function allOppose() {
    	return $this->opposeChild()->with('allOppose');
    }
}
