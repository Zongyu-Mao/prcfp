<?php

namespace App\Home\Examination\ExamDebate;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\ExamDebate\ExamDebateGiveLike\ExamDebateGivenLikeEvent;
use App\Events\Examination\ExamDebate;

class ExamDebateStarRecord extends Model
{
    public $timestamps = false;
    protected $fillable = ['debate_id','user_id','username','star','object','createtime'];

    // 点赞
    protected function giveLike($debate_id,$user_id,$username,$star,$object,$createtime){
    	$result = ExamDebateStarRecord::create([
    		'debate_id' => $debate_id,
    		'user_id' 	=> $user_id,
    		'username' 	=> $username,
    		'star' 		=> $star,
    		'object' 	=> $object,
    		'createtime' 	=> $createtime
    	]);
    	if($result->id){
    		event(new ExamDebateGivenLikeEvent($result));
    	}
    	return $result->id ? '1':'0';
    }
}
