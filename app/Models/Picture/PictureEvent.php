<?php

namespace App\Models\Picture;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PictureEvent extends Model
{
    use HasFactory;
    protected $fillable = ['picture_id','user_id','username','content','createtime'];

    public $timestamps = false;

    // 事件的添加
    protected function eventAdd($picture_id,$user_id,$username,$content,$createtime){
    	$eventArray = array(
    		'picture_id'		=> $picture_id,
    		'user_id'	=> $user_id,
    		'username'	=> $username,
            'content'   => $content,
    		'createtime'	=> $createtime,
    		);
    	$pictureEvent = new PictureEvent;
    	$result = $pictureEvent -> fill($eventArray) -> save();
    	return $result;
    }
}
