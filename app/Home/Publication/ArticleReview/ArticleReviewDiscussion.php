<?php

namespace App\Home\Publication\ArticleReview;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\ArticleReview\ArticleReviewDiscussionCreatedEvent;
use App\Events\Publication\ArticleReview\ArticleReviewDiscussionRepliedEvent;

class ArticleReviewDiscussion extends Model
{
    public $timestamps = true;
    protected $fillable = ['rid','author_id','author','title','comment','pid','standpoint'];

    // 关联user获得author
    public function getAuthor(){
        return $this->belongsTo('App\Models\User','author_id','id');
    }

    // 得到对应的求助
    public function getReview(){
        return $this->belongsTo('App\Home\Publication\ArticleReview','rid','id');
    }

    //模型处理评审意见（中立和支持）的添加
    protected function reviewCommentAdd($reviewId,$author_id,$author,$title,$comment,$pid,$standpoint){
    	$discussionArray = array(
    		'rid'		=> $reviewId,
            'author_id' => $author_id,
    		'author'	=> $author,
    		'title'	    => $title,
    		'comment'	=> $comment,
    		'pid'		=> $pid,
            'standpoint'=> $standpoint,
    		);
    	$reviewDiscussion = new ArticleReviewDiscussion;
    	$result = $reviewDiscussion -> fill($discussionArray) -> save();
        if($reviewDiscussion->id && $pid == '0'){
            event(new ArticleReviewDiscussionCreatedEvent($reviewDiscussion));
        }elseif($reviewDiscussion->id && $pid != '0'){
            event(new ArticleReviewDiscussionRepliedEvent($reviewDiscussion));
        }
    	return $result ? '1':'0';
    }

    //处理讨论区的回复（这个回复是所有人都可以操作的，不限于协作小组，是无限级嵌套）
    protected function discussChild(){
    	return $this-> hasMany('App\Home\Publication\ArticleReview\ArticleReviewDiscussion','pid','id');
    }
    //该方法无效？
    // protected function getChildrenDiscuss(){
    // 	return $this->Enckpidiscuss()->with('getChildrenDiscuss');
    // }

    // static protected function getDiscuss(){
    // 	$discuss = self::OrderBy('id','Desc')->get();
    // 	$discuss = self::getAllDiscuss($discuss);
    // 	return $discuss;
    // }

    // static public function getAllDiscuss($data,$pid=0,$level=0){

    // 	$arr = array();
    // 	foreach($data as $item){
    // 		if($item -> pid == $pid){
    // 			$item -> level == $level;
    // 			$arr[] = $item;
    // 			$res = self::getAllDiscuss($data,$item->id,$level+1);
    // 			$arr = array_merge($arr,$res);
    // 		}
    // 	}
    // 	return $arr;
    // }
    public function allDiscuss() {
    	return $this->discussChild()->with('allDiscuss');
    }
}
