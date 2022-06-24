<?php

namespace App\Home\Publication\Article\ExtendedReading;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\Article\ArticleExtendedReading\ArticleExtendedReadingAddEvent;
use App\Events\Publication\Article\ArticleExtendedReading\ArticleExtendedReadingDeletedEvent;

class ArticleExtendedEntryReading extends Model
{
    public $timestamps = false;

    protected $fillable = ['aid','extended_id'];

    protected function articleExtendedEntryAdd($aid,$extended_id){
    	$result = ArticleExtendedEntryReading::create([
    		'aid'=> $aid,
    		'extended_id'=> $extended_id
    	]);
    	event(new ArticleExtendedReadingAddEvent($result));
    	return $result ? '1':'0';
    }

    protected function articleExtendedArticleAdd($aid,$extended_id){
        $result = ArticleExtendedArticleReading::create([
            'aid'=> $aid,
            'extended_id'=> $extended_id
        ]);
        event(new ArticleExtendedArticleReadingAddEvent($result));
        return $result ? '1':'0';
    }

    protected function articleExtendedExamAdd($aid,$extended_id){
        $result = ArticleExtendedExamReading::create([
            'aid'=> $aid,
            'extended_id'=> $extended_id
        ]);
        event(new ArticleExtendedExamReadingAddEvent($result));
        return $result ? '1':'0';
    }

    // 删除引用
    protected function articleExtendedEntryDelete($aid,$extended_id){
        $res = ArticleExtendedEntryReading::where([['aid',$aid],['extended_id',$extended_id]])->first();
        $result = ArticleExtendedEntryReading::where('id',$res->id)->delete();
        event(new ArticleExtendedReadingDeletedEvent($res));
        return $result?1:0;
    }
}
