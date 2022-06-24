<?php

namespace App\Home\Publication\Article\ExtendedReading;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\Article\ArticleExtendedReading\ArticleExtendedExamReadingAddEvent;
use App\Events\Publication\Article\ArticleExtendedReading\ArticleExtendedExamReadingDeletedEvent;

class ArticleExtendedExamReading extends Model
{
    public $timestamps = false;

    protected $fillable = ['aid','extended_id'];

    protected function articleExtendedExamAdd($aid,$extended_id){
    	$result = ArticleExtendedExamReading::create([
    		'aid'=> $aid,
    		'extended_id'=> $extended_id
    	]);
    	event(new ArticleExtendedExamReadingAddEvent($result));
    	return $result ? '1':'0';
    }

    // 删除引用
    protected function articleExtendedExamDelete($aid,$extended_id){
        $res = ArticleExtendedExamReading::where([['aid',$aid],['extended_id',$extended_id]])->first();
        $result = ArticleExtendedExamReading::where('id',$res->id)->delete();
        event(new ArticleExtendedExamReadingDeletedEvent($res));
        return $result?1:0;
    }
}
