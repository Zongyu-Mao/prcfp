<?php

namespace App\Home\Publication\Article;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\Article\Focus\ArticleCollectedEvent;
use App\Events\Publication\Article\Focus\ArticleCollectCanceledEvent;

class ArticleCollectUser extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id','article_id'];

    protected function articleCollect($user_id,$article_id){
    	$result = ArticleCollectUser::create([
    		'user_id'	=> $user_id,
    		'article_id'	=> $article_id
    	]);
        event(new ArticleCollectedEvent($result));
    	return $result->id;
    }

    // 用户取消收藏
    protected function articleCollectCancel($user_id,$article_id){
    	$res = ArticleCollectUser::where([['user_id',$user_id],['article_id',$article_id]])->first();
        $result = ArticleCollectUser::where('id',$res->id)->delete();
        event(new ArticleCollectCanceledEvent($res));
    	return $result;
    }

}
