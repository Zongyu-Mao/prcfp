<?php

namespace App\Home\Examination\Recommend;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\Recommend\ExamTemperatureUpdatedEvent;

class ExamTemperature extends Model
{
    protected $fillable = ['exam_id','temperature'];

    public $timestamps = true;

    // 关联试卷
    public function getExam(){
        return $this->belongsTo('App\Home\Examination\Exam','exam_id','id');
    }

    //写入、创建热度，由于创建会有初始热度，因此tem不default0
    protected function temperatureAdd($exam_id,$temperature) {
        $result = ExamTemperature::create([
            'exam_id'  	=> $exam_id,
            'temperature'  	=> $temperature
        ]);
        return $result->id;
    }

    //更新热度
    protected function recommendationUpdate($id,$temperature) {
        $result = ExamTemperature::where('id',$id)->update([
            'temperature'  	=> $temperature
        ]);
        // 这里要引发的是热度更新的事件，检查是否需要更新推荐表，目前推荐表考虑实时更新
        event(new ExamTemperatureUpdatedEvent(ExamTemperature::find($id)));
        return $result ? '1':'0';
    }

    // 初始化热度记录
    protected function recordInitialization($exam_id){
    	if(!ExamTemperature::where('exam_id',$exam_id)->exists()){
    		$result = ExamTemperature::create([
	            'exam_id'  	=> $exam_id,
	            'temperature'  	=> 0
	        ]);
	    return $result;
    	}
    }
}
