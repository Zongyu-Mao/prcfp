<?php

namespace App\Home\Publication\ArticleDiscussion;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\ArticleDiscussion\ArticleAdvisementCreatedEvent;
use App\Events\Publication\ArticleDiscussion\ArticleAdvisementAcceptedEvent;
use App\Events\Publication\ArticleDiscussion\ArticleAdvisementRejectedEvent;

class ArticleAdvise extends Model
{
    protected $fillable = ['aid','deadline','title','comment','pid','author_id','author','recipient_id','recipient','round','status'];

    // 一对一关联,获得著作信息
    public function getArticle(){
        return $this->belongsTo('App\Home\Publication\Article','aid','id');
    }

    //写入词条建议立场的讨论信息
     protected function adviseAdd($aid,$deadline,$title,$comment,$pid,$author_id,$author,$round){
    	$adviseArray = array(
    		'aid'		=> $aid,
    		'title'		=> $title,
    		'deadline'	=> $deadline,
    		'comment'	=> $comment,
    		'pid'		=> $pid,
    		'author_id'	=> $author_id,
            'author'    => $author,
    		'round'	=> $round,
    		);
    	$articleAdvise = new ArticleAdvise;
    	$result = $articleAdvise -> fill($adviseArray) -> save();
        if($articleAdvise->id){
            event(new ArticleAdvisementCreatedEvent($articleAdvise));
        }
    	return $result ? '1':'0';
    }

    //写入词条建议的拒绝信息
     protected function rejectAdd($aid,$deadline,$title,$comment,$pid,$author_id,$author,$recipient_id,$recipient,$round){
        $adviseArray = array(
            'aid'    	=> $aid,
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
        $articleAdvise = new ArticleAdvise;
        $result = $articleAdvise -> fill($adviseArray) -> save();
        if($articleAdvise->id){
            ArticleAdvise::where('id',$articleAdvise->pid)->update([
                    'recipient_id'  => $author_id,
                    'recipient'     => $author,
                    'status'        => 2,
                ]);
            event(new ArticleAdvisementRejectedEvent($articleAdvise));
        }
        return $result ? '1':'0';
    }

    // 接受建议的处理
     protected function adviseAccept($id,$recipient_id,$recipient,$status){
        $result = ArticleAdvise::where('id',$id)->update([
                'recipient_id'  => $recipient_id,
                'recipient'     => $recipient,
                'status'        => $status,
            ]);
        if($result){
            event(new ArticleAdvisementAcceptedEvent(ArticleAdvise::find($id)));
        }
        return $result ? '1':'0';
     }

    //处理建议区的回复
    protected function adviseChild(){
        return $this-> hasMany('App\Home\Publication\ArticleDiscussion\ArticleAdvise','pid','id');
    }

    public function allAdvise() {
        return $this->adviseChild()->with('allAdvise');
    }
}
