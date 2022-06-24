<?php

namespace App\Models\Vote;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoteEvent extends Model
{
    use HasFactory;
    protected $fillable = ['vid','user_id','username','content','createtime'];
    public $timestamps = false;

    // 事件的添加
    protected function voteEventAdd($vid,$user_id,$username,$content,$createtime){
    	$eventArray = array(
    		'vid'		=> $vid,
    		'user_id'	=> $user_id,
    		'username'	=> $username,
            'content'   => $content,
    		'createtime'	=> $createtime,
    		);
    	$voteEvent = new VoteEvent;
    	$result = $voteEvent -> fill($eventArray) -> save();
    	return $result;
    }
}
