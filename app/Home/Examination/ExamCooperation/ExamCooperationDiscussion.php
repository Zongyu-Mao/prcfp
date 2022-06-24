<?php

namespace App\Home\Examination\ExamCooperation;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\ExamCooperation\Discussion\DiscussionCreatedEvent;

class ExamCooperationDiscussion extends Model
{
    protected $fillable = ['cooperation_id','comment','author_id','author'];

	// 发表讨论
	protected function discussionAdd($cooperation_id,$comment,$author_id,$author){
		$result = ExamCooperationDiscussion::create([
            'cooperation_id'=> $cooperation_id,
            'comment' 		=> $comment,
            'author_id' 	=> $author_id,
            'author' 		=>  $author,
        ]);
        if($result->id){
        	event (new DiscussionCreatedEvent($result));
        }
        return $result->id ? '1':'0';
	}
}
