<?php

namespace App\Home\Publication\Article;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\Article\Focus\ArticleFocusedEvent;
use App\Events\Publication\Article\Focus\ArticleFocusCanceledEvent;

class ArticleFocusUser extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id','article_id'];

    // 用户关注著作
    protected function articleFocus($user_id,$article_id){
    	$result = ArticleFocusUser::create([
    		'user_id'	=> $user_id,
    		'article_id'	=> $article_id
    	]);
        event(new ArticleFocusedEvent($result));
    	return $result->id;
    }

    // 用户取消关注
    protected function articleFocusCancel($user_id,$article_id){
    	$res = ArticleFocusUser::where([['user_id',$user_id],['article_id',$article_id]])->first();
        $result = ArticleFocusUser::where('id',$res->id)->delete();
        event(new ArticleFocusCanceledEvent($res));
    	return $result;
    }

}
