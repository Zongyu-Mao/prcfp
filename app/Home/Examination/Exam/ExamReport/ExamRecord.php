<?php

namespace App\Home\Examination\Exam\ExamReport;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\ExamReport\ExamRecordAddEvent;

class ExamRecord extends Model
{
    public $timestamps = false;

    protected $fillable = ['exam_id','user_id','rate','score','createtime'];

    protected function examRecordAdd($exam_id,$user_id,$rate,$score,$createtime){
    	$result = ExamRecord::create([
    		'exam_id'		=> $exam_id,
    		'user_id'	=> $user_id,
    		'rate'	=> $rate,
    		'score'		=> $score,
    		'createtime'=> $createtime
    	]);
    	event(new ExamRecordAddEvent($result));
    	return $result->id;
    }
}
