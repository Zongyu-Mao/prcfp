<?php

namespace App\Home\Publication\ArticleDebate;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\ArticleDebate\ArticleDebateComment\ArticleDebateCommentCreatedEvent;

class ArticleDebateComment extends Model
{
    protected $fillable = ['aid','debate_id','comment','pid','author_id','author','title','up','down','type'];

    // 关联user获得author
    public function getAuthor(){
        return $this->belongsTo('App\Models\User','author_id','id');
    }

    // 得到对应的求助
    public function getDebate(){
        return $this->belongsTo('App\Home\Publication\ArticleDebate','debate_id','id');
    }

    //评论内容的添加
    protected function commentAdd($aid,$debate_id,$comment,$pid,$author_id,$author,$title,$type){
    	$result = ArticleDebateComment::create([
    		'aid'		=> $aid,
    		'debate_id'	=> $debate_id,
    		'comment'	=> $comment,
    		'pid'		=> $pid,
            'author_id' => $author_id,
    		'author'	=> $author,
            'title'     => $title,
    		'type'		=> $type,
    	]);
    	event(new ArticleDebateCommentCreatedEvent($result));
    	return $result ? '1':'0';
    }

    //评论内容的获取
    protected function commentChild(){
        return $this-> hasMany('App\Home\Publication\ArticleDebate\ArticleDebateComment','pid','id');
    }

    public function allComment() {
        return $this->commentChild()->with('allComment');
    }
}
