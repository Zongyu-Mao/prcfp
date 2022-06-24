<?php

namespace App\Home\Publication\ArticleCooperation;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\ArticleCooperation\ArticleCooperationDiscussionCreatedEvent;

class ArticleCooperationDiscussion extends Model
{
	protected $fillable = ['cooperation_id','comment','author_id','author'];

    // 关联user获得author
    public function getAuthor(){
        return $this->belongsTo('App\Models\User','author_id','id');
    }

    // 获得父级
    public function getCooperation(){
        return $this->belongsTo('App\Home\Publication\ArticleCooperation','cooperation_id','id');
    }

	// 发表讨论
	protected function discussionAdd($cooperation_id,$comment,$author_id,$author){
		$result = ArticleCooperationDiscussion::create([
            'cooperation_id'=> $cooperation_id,
            'comment' 		=> $comment,
            'author_id' 	=> $author_id,
            'author' 		=>  $author,
        ]);
        if($result->id){
        	event (new ArticleCooperationDiscussionCreatedEvent($result));
        }
        return $result->id ? '1':'0';
	}
}
