<?php

namespace App\Home\Examination\Exam;

use Illuminate\Database\Eloquent\Model;

class ExamDynamic extends Model
{
    //著作动态的新建
    public $timestamps = false;

    protected $fillable = ['exam_id','examTitle','behavior','objectName','objectURL','createtime'];

    // 添加用户动态事件
    protected function dynamicAdd($exam_id,$examTitle,$behavior,$objectName,$objectURL,$createtime){
    	$result = ExamDynamic::create([
            'exam_id'   => $exam_id,
            'examTitle'=> $examTitle,
            'behavior'  => $behavior,
            'objectName'=> $objectName,
            'objectURL' => $objectURL,
            'createtime'=>$createtime,
        ]);
        return $result->id;
    }
}
