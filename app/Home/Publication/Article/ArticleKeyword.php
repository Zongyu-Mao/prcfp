<?php

namespace App\Home\Publication\Article;

use Illuminate\Database\Eloquent\Model;

class ArticleKeyword extends Model
{

	public $timestamps = false;

    protected $fillable = ['keyword_id','article_id'];

    // 向表中插入新的关键词与著作关系
    protected function keywordAdd($keyword_id,$article_id){
    	$result = ArticleKeyword::create([
    		'keyword_id'	=> $keyword_id,
    		'article_id'	=> $article_id
    	]);
        // event(new ArticleKyewordModifiedEvent($result));
    	return $result->id;
    }
}
