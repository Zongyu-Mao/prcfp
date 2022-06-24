<?php

namespace App\Home\Encyclopedia\EntryDiscussion;

use Illuminate\Database\Eloquent\Model;
use App\Events\Encyclopedia\EntryDiscussion\EntryOpponentCreatedEvent;
use App\Events\Encyclopedia\EntryDiscussion\EntryOpponentAcceptedEvent;
use App\Events\Encyclopedia\EntryDiscussion\EntryOpponentRejectedEvent;

class EntryOpponent extends Model
{
    protected $fillable = ['eid','deadline','title','comment','pid','author_id','author','recipient_id','recipient','stars','round','status'];
    //输出词条讨论信息

    //一对一关联，获得词条信息
    public function getEntry(){
        return $this->belongsTo('App\Home\Encyclopedia\Entry','eid','id');
    }

    // 一对一关联组长信息
    public function managerInfo(){
        return $this->belongsTo('App\Models\User','author_id','id');
    }

    //写入词条反对立场的讨论信息
    protected function opponentAdd($eid,$deadline,$title,$comment,$pid,$author_id,$author,$round){
    	$opposeArray = array(
    		'eid'	=> $eid,
    		'title'		=> $title,
    		'deadline'	=> $deadline,
    		'comment'	=> $comment,
    		'pid'		=> $pid,
    		'author_id'	=> $author_id,
            'author'    => $author,
            'round'     => $round,
    		);
    	$entryOpponent = new EntryOpponent;
    	$result = $entryOpponent -> fill($opposeArray) -> save();
        if($entryOpponent->id){
            event(new EntryOpponentCreatedEvent($entryOpponent));
        }
    	return $result ? '1':'0';
    }

    //处理反对意见的拒绝机制
    protected function rejectAdd($eid,$deadline,$title,$comment,$pid,$author_id,$author,$recipient_id,$recipient,$round){
        $opposeArray = array(
            'eid'    => $eid,
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
        $entryOpponent = new EntryOpponent;
        $result = $entryOpponent -> fill($opposeArray) -> save();
        if($entryOpponent->id){
            EntryOpponent::where('id',$pid)->update([
                    'recipient_id'  => $author_id,
                    'recipient'     => $author,
                    'status'        => 2,
                ]);
            event(new EntryOpponentRejectedEvent(EntryOpponent::find($entryOpponent->id)));
        }
        return $result ? '1':'0';
    }

    // 处理反对意见的接收
    protected function rejectAccept($id,$recipient_id,$recipient,$status){
        $result = EntryOpponent::where('id',$id)->update([
                'recipient_id'  => $recipient_id,
                'recipient'     => $recipient,
                'status'        => $status,
            ]);
        if($result){
            event(new EntryOpponentAcceptedEvent(EntryOpponent::find($id)));
        }
        return $result ? '1':'0';
    }
    //处理反对区的回复
    protected function opposeChild(){
        return $this-> hasMany('App\Home\Encyclopedia\EntryDiscussion\EntryOpponent','pid','id');
    }

    public function allOppose() {
        return $this->opposeChild()->with('allOppose');
    }
}
