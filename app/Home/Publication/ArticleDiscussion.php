<?php

namespace App\Home\Publication;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\ArticleDiscussion\ArticleDiscussionCreatedEvent;
use App\Events\Publication\ArticleDiscussion\ArticleDiscussionRepliedEvent;

class ArticleDiscussion extends Model
{
    protected $fillable = ['aid','title','comment','pid','author_id','author','recipient_id','recipient','stars','round','status'];

    // 一对一关联,获得著作信息
    public function getArticle(){
        return $this->belongsTo('App\Home\Publication\Article','aid','id');
    }

    //写入词条讨论信息
     protected function discussionAdd($aid,$title,$comment,$pid,$author_id,$author){
    	$discussionArray = array(
    		'aid'	=> $aid,
    		'title'		=> $title,
    		'comment'	=> $comment,
    		'pid'		=> $pid,
    		'author_id'	=> $author_id,
    		'author'	=> $author,
    		);
    	$entryDiscussion = new ArticleDiscussion;
    	$result = $entryDiscussion -> fill($discussionArray) -> save();
        if($entryDiscussion->id && $pid == '0'){
            event(new ArticleDiscussionCreatedEvent($entryDiscussion));
        }elseif($entryDiscussion->id && $pid != '0'){
            event(new ArticleDiscussionRepliedEvent($entryDiscussion));
        }
    	return $result ? '1':'0';
    }

    //处理讨论区的显示
    protected function discussChild(){
        return $this-> hasMany('App\Home\Publication\ArticleDiscussion','pid','id');
    }

    public function allDiscuss() {
        return $this->discussChild()->with('allDiscuss');
    }
}
