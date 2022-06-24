<?php

namespace App\Home\Encyclopedia\EntryDebate;

use Illuminate\Database\Eloquent\Model;
use App\Events\Encyclopedia\EntryDebate\EntryDebateGiveLike\EntryDebateGivenLikeEvent;
use App\Events\Encyclopedia\EntryDebate;

class EntryDebateStarRecord extends Model
{
	public $timestamps = false;
    protected $fillable = ['debate_id','user_id','username','star','object','createtime'];

    // 点赞
    protected function giveLike($debate_id,$user_id,$username,$star,$object,$createtime){
    	$result = EntryDebateStarRecord::create([
    		'debate_id' => $debate_id,
    		'user_id' 	=> $user_id,
    		'username' 	=> $username,
    		'star' 		=> $star,
    		'object' 	=> $object,
    		'createtime' 	=> $createtime
    	]);
    	if($result->id){
    		event(new EntryDebateGivenLikeEvent($result));
    	}
    	return $result->id ? '1':'0';
    }
}
