<?php

namespace App\Home\Encyclopedia\EntryCooperation;

use Illuminate\Database\Eloquent\Model;
use App\Events\Encyclopedia\EntryCooperation\EntryCooperationDiscussionCreatedEvent;

class EntryCooperationDiscussion extends Model
{
    //
	protected $fillable = ['cooperation_id','comment','author_id','author'];

	// 发表讨论
	protected function discussionAdd($cooperation_id,$comment,$author_id,$author){
		$result = EntryCooperationDiscussion::create([
            'cooperation_id'=> $cooperation_id,
            'comment' 		=> $comment,
            'author_id' 	=> $author_id,
            'author' 		=>  $author,
        ]);
        if($result->id){
        	event (new EntryCooperationDiscussionCreatedEvent($result));
        }
        return $result->id ? '1':'0';
	}
}
