<?php

namespace App\Home\Publication\Article\ExtendedReading;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\Article\ArticleExtendedReading\ArticleExtendedArticleReadingAddEvent;
use App\Events\Publication\Article\ArticleExtendedReading\ArticleExtendedArticleReadingDeletedEvent;

class ArticleExtendedArticleReading extends Model
{
    public $timestamps = false;

    protected $fillable = ['aid','extended_id'];

    protected function articleExtendedArticleAdd($aid,$extended_id){
    	$result = ArticleExtendedArticleReading::create([
    		'aid'=> $aid,
    		'extended_id'=> $extended_id
    	]);
    	event(new ArticleExtendedArticleReadingAddEvent($result));
    	return $result ? '1':'0';
    }

    // 删除引用
    protected function articleExtendedArticleDelete($aid,$extended_id){
        $res = ArticleExtendedArticleReading::where([['aid',$aid],['extended_id',$extended_id]])->first();
        $result = ArticleExtendedArticleReading::where('id',$res->id)->delete();
        event(new ArticleExtendedArticleReadingDeletedEvent($res));
        return $result?1:0;
    }
}
