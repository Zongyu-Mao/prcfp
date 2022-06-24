<?php

namespace App\Home\Publication\ArticleResort;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\ArticleResort\ArticleResortSupportCommentCreatedEvent;

class ArticleResortSupportComment extends Model
{
    protected $fillable = ['aid','resortId','comment','pid','author_id','title','type'];

    // 关联user获得author
    public function getAuthor(){
        return $this->belongsTo('App\Models\User','author_id','id');
    }

    // 得到对应的求助
    public function getResort(){
        return $this->belongsTo('App\Home\Publication\ArticleResort','resortId','id');
    }
    
    //写入新建的帮助评论内容
    protected function commentAdd($aid,$resortId,$comment,$pid,$title,$author_id){
    	$result = ArticleResortSupportComment::create([
    		'aid'	=> $aid,
    		'resortId'	=> $resortId,  		
    		'comment'	=> $comment,
    		'pid'		=> $pid,
            'title'     => $title,
            'author_id'	=> $author_id,
    	]);
        event(new ArticleResortSupportCommentCreatedEvent($result));
    	return $result ? '1':'0';
    }

    //求助者拒绝帮助的评论内容
    protected function rejectCommentAdd($aid,$resortId,$comment,$pid,$title,$author_id,$type){
    	$commentArray = array(
    		'aid'	=> $aid,
    		'resortId'	=> $resortId,  		
    		'comment'	=> $comment,
    		'pid'		=> $pid,
            'title'     => $title,
            'author_id'	=> $author_id,
            'type'	=> $type,
    		);
    	$supportComment = new ArticleResortSupportComment;
    	$result = $supportComment -> fill($commentArray) -> save();
    	return $result ? '1':'0';
    }

    //处理评论的无限分级
    protected function commentChild(){
        return $this-> hasMany('App\Home\Publication\ArticleResort\ArticleResortSupportComment','pid','id');
    }

    public function allComment() {
        return $this->commentChild()->with('allComment');
    }
}
