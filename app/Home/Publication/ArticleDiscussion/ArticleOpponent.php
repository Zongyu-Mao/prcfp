<?php

namespace App\Home\Publication\ArticleDiscussion;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\ArticleDiscussion\ArticleOpponentCreatedEvent;
use App\Events\Publication\ArticleDiscussion\ArticleOpponentAcceptedEvent;
use App\Events\Publication\ArticleDiscussion\ArticleOpponentRejectedEvent;

class ArticleOpponent extends Model
{
    protected $fillable = ['aid','deadline','title','comment','pid','author_id','author','recipient_id','recipient','stars','round','status'];
    
    //一对一关联，获得著作信息
    public function getArticle(){
        return $this->belongsTo('App\Home\Publication\Article','aid','id');
    }

    // 一对一关联组长信息
    public function managerInfo(){
        return $this->belongsTo('App\Models\User','author_id','id');
    }

    //写入词条反对立场的讨论信息
    protected function opponentAdd($aid,$deadline,$title,$comment,$pid,$author_id,$author,$round){
    	$opposeArray = array(
    		'aid'	=> $aid,
    		'title'		=> $title,
    		'deadline'	=> $deadline,
    		'comment'	=> $comment,
    		'pid'		=> $pid,
    		'author_id'	=> $author_id,
            'author'    => $author,
            'round'     => $round,
    		);
    	$articleOpponent = new ArticleOpponent;
    	$result = $articleOpponent -> fill($opposeArray) -> save();
        if($articleOpponent->id){
            event(new ArticleOpponentCreatedEvent($articleOpponent));
        }
    	return $result ? '1':'0';
    }

    //处理反对意见的拒绝机制
    protected function rejectAdd($aid,$deadline,$title,$comment,$pid,$author_id,$author,$recipient_id,$recipient,$round){
        $opposeArray = array(
            'aid'    => $aid,
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
        $articleOpponent = new ArticleOpponent;
        $result = $articleOpponent -> fill($opposeArray) -> save();
        if($articleOpponent->id){
            ArticleOpponent::where('id',$pid)->update([
                    'recipient_id'  => $author_id,
                    'recipient'     => $author,
                    'status'        => 2,
                ]);
            event(new ArticleOpponentRejectedEvent(ArticleOpponent::find($articleOpponent->id)));
        }
        return $result ? '1':'0';
    }

    // 处理反对意见的接收
    protected function rejectAccept($id,$recipient_id,$recipient,$status){
        $result = ArticleOpponent::where('id',$id)->update([
                'recipient_id'  => $recipient_id,
                'recipient'     => $recipient,
                'status'        => 1,
            ]);
        if($result){
            event(new ArticleOpponentAcceptedEvent(ArticleOpponent::find($id)));
        }
        return $result ? '1':'0';
    }
    //处理反对区的回复
    protected function opposeChild(){
        return $this-> hasMany('App\Home\Publication\ArticleDiscussion\ArticleOpponent','pid','id');
    }

    public function allOppose() {
        return $this->opposeChild()->with('allOppose');
    }
}
