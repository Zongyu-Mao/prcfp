<?php

namespace App\Home\Encyclopedia\EntryDiscussion;

use Illuminate\Database\Eloquent\Model;
use App\Events\Encyclopedia\EntryDiscussion\EntryAdvisementCreatedEvent;
use App\Events\Encyclopedia\EntryDiscussion\EntryAdvisementAcceptedEvent;
use App\Events\Encyclopedia\EntryDiscussion\EntryAdvisementRejectedEvent;

class EntryAdvise extends Model
{
    protected $fillable = ['eid','deadline','title','comment','pid','author_id','author','recipient_id','recipient','round','status'];

    // 一对一关联词条,获得词条信息
    public function getEntry(){
        return $this->belongsTo('App\Home\Encyclopedia\Entry','eid','id');
    }

    //写入词条建议立场的讨论信息
    protected function adviseAdd($eid,$deadline,$title,$comment,$pid,$author_id,$author,$round){
    	$adviseArray = array(
    		'eid'		=> $eid,
    		'title'		=> $title,
    		'deadline'	=> $deadline,
    		'comment'	=> $comment,
    		'pid'		=> $pid,
    		'author_id'	=> $author_id,
            'author'    => $author,
    		'round'	=> $round,
    		);
    	$entryAdvise = new EntryAdvise;
    	$result = $entryAdvise -> fill($adviseArray) -> save();
        if($entryAdvise->id){
            event(new EntryAdvisementCreatedEvent($entryAdvise));
        }
    	return $result ? '1':'0';
    }

    //写入词条建议的拒绝信息
    protected function rejectAdd($eid,$deadline,$title,$comment,$pid,$author_id,$author,$recipient_id,$recipient,$round){
        $adviseArray = array(
            'eid'    	=> $eid,
            'title'     => $title,
            'deadline'  => $deadline,
            'comment'   => $comment,
            'pid'       => $pid,
            'author_id' => $author_id,
            'author'    => $author,
            'recipient_id'     => $recipient_id,
            'recipient'        => $recipient,
            'round'     => $round,
            );
        $entryAdvise = new EntryAdvise;
        $result = $entryAdvise -> fill($adviseArray) -> save();
        if($entryAdvise->id){
            EntryAdvise::where('id',$entryAdvise->pid)->update([
                    'recipient_id'  => $author_id,
                    'recipient'     => $author,
                    'status'        => 2,
                ]);
            event(new EntryAdvisementRejectedEvent($entryAdvise));
        }
        return $result ? '1':'0';
    }

    // 接受建议的处理
    protected function adviseAccept($id,$recipient_id,$recipient,$status){
        $result = EntryAdvise::where('id',$id)->update([
                'recipient_id'  => $recipient_id,
                'recipient'     => $recipient,
                'status'        => $status,
            ]);
        if($result){
            event(new EntryAdvisementAcceptedEvent(EntryAdvise::find($id)));
        }
        return $result ? '1':'0';
     }

    //处理建议区的回复
    protected function adviseChild(){
        return $this-> hasMany('App\Home\Encyclopedia\EntryDiscussion\EntryAdvise','pid','id');
    }

    public function allAdvise() {
        return $this->adviseChild()->with('allAdvise');
    }
}
