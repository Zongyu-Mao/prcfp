<?php

namespace App\Home\Examination\Exam\Extended;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\ExtendedReading\ExamExtendedExamAddedEvent;
use App\Events\Examination\ExtendedReading\ExamExtendedExamDeletedEvent;

class ExamExtendedExam extends Model
{
    public $timestamps = false;

    protected $fillable = ['exam_id','extended_id'];

    protected function examExtendedExamAdd($exam_id,$extended_id){
    	$result = ExamExtendedExam::create([
    		'exam_id'=> $exam_id,
    		'extended_id'=> $extended_id
    	]);
    	event(new ExamExtendedExamAddedEvent($result));
    	return $result->id ? '1':'0';
    }

    // 删除引用
    protected function examExtendedExamDelete($exam_id,$extended_id){
        $res = ExamExtendedExam::where([['exam_id',$exam_id],['extended_id',$extended_id]])->first();
        $result = ExamExtendedExam::where('id',$res->id)->delete();
        event(new ExamExtendedExamDeletedEvent($res));
        return $result?1:0;
    }
}
