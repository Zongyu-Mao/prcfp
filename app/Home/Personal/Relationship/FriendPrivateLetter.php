<?php

namespace App\Home\Personal\Relationship;

use Illuminate\Database\Eloquent\Model;
use App\Events\Personal\Relationship\FriendPrivateLetterSentEvent;
use App\Events\Personal\Relationship\FriendPrivateLetterRepliedEvent;

class FriendPrivateLetter extends Model
{
    protected $fillable = ['from_id','from_username','to_id','to_username','title','content','pid','status'];

    // 得到私信
    public function reply() {
        return $this-> hasOne('App\Home\Personal\Relationship\FriendPrivateLetter','pid','id');
    }

    // 发送站内信
    protected function friendPrivateLetterSend($from_id,$from_username,$to_id,$to_username,$title,$content,$pid) {
        $result = FriendPrivateLetter::create([
            'from_id'   => $from_id,
            'from_username'	=> $from_username,
            'to_id'			=> $to_id,
            'to_username'	=> $to_username,
            'title'		=> $title,
            'content'	=> $content,
            'pid'		=> $pid,
        ]);
        // 申请写入成功，触发申请事件
        if($result->id && $pid == '0'){
        	event(new FriendPrivateLetterSentEvent($result));
        }elseif($result->id && $pid != '0'){
        	event(new FriendPrivateLetterRepliedEvent($result));
        }
        return $result->id ? '1':'0';
    }
}
