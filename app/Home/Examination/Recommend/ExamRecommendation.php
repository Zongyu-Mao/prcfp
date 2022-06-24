<?php

namespace App\Home\Examination\Recommend;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\Recommend\ExamRecommendationUpdatedEvent;

class ExamRecommendation extends Model
{
    protected $fillable = ['cid','exam_id'];

    public $timestamps = true;

    //写入
    protected function recommendationAdd($cid,$exam_id) {
        $result = ExamRecommendation::create([
            'cid'  	=> $cid,
            'exam_id'  	=> $exam_id,
        ]);
        event(new ExamRecommendationUpdatedEvent($result));
        return $result;
    }

    //修改
    protected function recommendationUpdate($id,$exam_id) {
        $result = ExamRecommendation::where('id',$id)->update([
            'exam_id'  	=> $exam_id,
        ]);
        event(new ExamRecommendationUpdatedEvent(ExamRecommendation::find($id)));
        return $result ? '1':'0';
    }
}
