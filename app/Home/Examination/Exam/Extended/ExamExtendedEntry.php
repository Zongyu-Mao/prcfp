<?php

namespace App\Home\Examination\Exam\Extended;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\ExtendedReading\ExamExtendedEntryAddedEvent;
use App\Events\Examination\ExtendedReading\ExamExtendedEntryDeletedEvent;

class ExamExtendedEntry extends Model
{
    public $timestamps = false;

    protected $fillable = ['exam_id','extended_id'];

    protected function examExtendedEntryAdd($exam_id,$extended_id){
    	$result = ExamExtendedEntry::create([
    		'exam_id'=> $exam_id,
    		'extended_id'=> $extended_id
    	]);
    	event(new ExamExtendedEntryAddedEvent($result));
    	return $result->id ? '1':'0';
    }

    // 删除引用
    protected function examExtendedEntryDelete($exam_id,$extended_id){
        $res = ExamExtendedEntry::where([['exam_id',$exam_id],['extended_id',$extended_id]])->first();
        $result = ExamExtendedEntry::where('id',$res->id)->delete();
        event(new ExamExtendedEntryDeletedEvent($res));
        return $result?1:0;
    }
}
