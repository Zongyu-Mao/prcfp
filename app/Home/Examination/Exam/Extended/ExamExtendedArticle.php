<?php

namespace App\Home\Examination\Exam\Extended;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\ExtendedReading\ExamExtendedArticleAddedEvent;
use App\Events\Examination\ExtendedReading\ExamExtendedArticleDeletedEvent;

class ExamExtendedArticle extends Model
{
    public $timestamps = false;

    protected $fillable = ['exam_id','extended_id'];

    protected function examExtendedArticleAdd($exam_id,$extended_id){
    	$result = ExamExtendedArticle::create([
    		'exam_id'=> $exam_id,
    		'extended_id'=> $extended_id
    	]);
    	event(new ExamExtendedArticleAddedEvent($result));
    	return $result->id ? '1':'0';
    }

    // 删除引用
    protected function examExtendedArticleDelete($exam_id,$extended_id){
        $res = ExamExtendedArticle::where([['exam_id',$exam_id],['extended_id',$extended_id]])->first();
        $result = ExamExtendedArticle::where('id',$res->id)->delete();
        event(new ExamExtendedArticleDeletedEvent($res));
        return $result?1:0;
    }
}
