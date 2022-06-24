<?php

namespace App\Home\Publication\ArticleDebate;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\ArticleDebate\ArticleDebateGiveLike\ArticleDebateGivenLikeEvent;
use App\Events\Publication\ArticleDebate;

class ArticleDebateStarRecord extends Model
{
    public $timestamps = false;
    protected $fillable = ['debate_id','user_id','username','star','object','createtime'];

    // 点赞
    protected function giveLike($debate_id,$user_id,$username,$star,$object,$createtime){
    	$result = ArticleDebateStarRecord::create([
    		'debate_id' => $debate_id,
    		'user_id' 	=> $user_id,
    		'username' 	=> $username,
    		'star' 		=> $star,
    		'object' 	=> $object,
    		'createtime' 	=> $createtime
    	]);
    	if($result->id){
    		event(new ArticleDebateGivenLikeEvent($result));
    	}
    	return $result->id ? '1':'0';
    }
}
