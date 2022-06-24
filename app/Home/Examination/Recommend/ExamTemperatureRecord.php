<?php

namespace App\Home\Examination\Recommend;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\Recommend\ExamTemperatureRecordAddedEvent;

class ExamTemperatureRecord extends Model
{
    protected $fillable = ['exam_id','user_id','behavior_id','createtime'];

    public $timestamps = false;

    // 新建热度记录
    protected function recordAdd($exam_id,$user_id,$behavior_id,$createtime){
     	$result = ExamTemperatureRecord::create([
     		'exam_id'	=> $exam_id,
     		'user_id'	=> $user_id,
     		'behavior_id'	=> $behavior_id,
     		'createtime'	=> $createtime,
     	]);
     	// 热度记录新建后触发对应事件
     	event(new ExamTemperatureRecordAddedEvent($result));
     	return $result->id;
    }
}
