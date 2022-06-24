<?php

namespace App\Home\Encyclopedia;

use Illuminate\Database\Eloquent\Model;
use App\Events\Encyclopedia\EntryDiscussion\EntryDiscussionCreatedEvent;
use App\Events\Encyclopedia\EntryDiscussion\EntryDiscussionRepliedEvent;

class EntryDiscussion extends Model
{
    protected $fillable = ['eid','title','comment','pid','author_id','author','recipient_id','recipient','stars','round','status'];

    //输出词条讨论信息
    //一对一关联，获得词条信息
    public function getEntry(){
        return $this->belongsTo('App\Home\Encyclopedia\Entry','eid','id');
    }

    //写入词条讨论信息
     protected function discussionAdd($eid,$title,$comment,$pid,$author_id,$author){
    	$discussionArray = array(
    		'eid'	=> $eid,
    		'title'		=> $title,
    		'comment'	=> $comment,
    		'pid'		=> $pid,
    		'author_id'	=> $author_id,
    		'author'	=> $author,
    		);
    	$entryDiscussion = new EntryDiscussion;
    	$result = $entryDiscussion -> fill($discussionArray) -> save();
        if($entryDiscussion->id && $pid == '0'){
            event(new EntryDiscussionCreatedEvent($entryDiscussion));
        }elseif($entryDiscussion->id && $pid != '0'){
            event(new EntryDiscussionRepliedEvent($entryDiscussion));
        }
    	return $result ? '1':'0';
    }

    //处理讨论区的显示
    protected function discussChild(){
        return $this-> hasMany('App\Home\Encyclopedia\EntryDiscussion','pid','id');
    }

    public function allDiscuss() {
        return $this->discussChild()->with('allDiscuss');
    }
}
