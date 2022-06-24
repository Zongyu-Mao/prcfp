<?php

namespace App\Home\Examination\Recommend;

use Illuminate\Database\Eloquent\Model;

class ExamRecommendRecord extends Model
{
    protected $fillable = ['cid','exam_id','createtime'];

    public $timestamps = false;

    //写入推荐的记录，这是每一次上推荐表的记录
    protected function recordAdd($cid,$exam_id,$createtime) {
        $result = ExamRecommendRecord::create([
            'cid'   => $cid,
            'exam_id'  	=> $exam_id,
            'createtime'  	=> $createtime
        ]);
        return $result->id;
    }
}
