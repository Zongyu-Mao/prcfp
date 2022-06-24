<?php

namespace App\Home\Organization\Group\GroupDoc;

use Illuminate\Database\Eloquent\Model;
use App\Events\Organization\Group\GroupDoc\GroupDocCommentCreatedEvent;
use App\Events\Organization\Group\GroupDoc\GroupDocCommentRepliedEvent;

class GroupDocComment extends Model
{
	// public $timestamps = true;

    protected $fillable = ['did','title','comment','pid','author_id','author','status'];

    // 找到文档
    public function getDoc(){
        return $this->belongsTo('App\Home\Organization\Group\GroupDoc','did','id');
    }
    
    //添加评论
    protected function commentAdd($did,$title,$comment,$pid,$author_id,$author){
    	$result = GroupDocComment::create([
            'did'   => $did,
            'title' => $title,
            'comment'	=> $comment,
            'pid'		=> $pid,
            'author_id'	=> $author_id,
            'author'	=>$author,
        ]);
        event(new GroupDocCommentCreatedEvent($result));
        return $result->id;
    }

    //回复评论
    protected function commentReply($did,$title,$comment,$pid,$author_id,$author){
        $result = GroupDocComment::create([
            'did'   => $did,
            'title' => $title,
            'comment'   => $comment,
            'pid'       => $pid,
            'author_id' => $author_id,
            'author'    =>$author,
        ]);
        event(new GroupDocCommentRepliedEvent($result));
        return $result->id;
    }

    // 所有分级的分类
    protected function commentChild(){
        return $this-> hasMany('App\Home\Organization\Group\GroupDoc\GroupDocComment','pid','id');
    }

    public function allComment() {
        return $this->commentChild()->with('allComment');
    }
}
