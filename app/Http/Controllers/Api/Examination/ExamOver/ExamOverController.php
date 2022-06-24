<?php

namespace App\Http\Controllers\Api\Examination\ExamOver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Home\Examination\Exam;
use App\Home\Examination\Exam\ExamReport\ExamRecord;
use Carbon\Carbon;

class ExamOverController extends Controller
{
    //上传考试分数
    public function uploadScore(Request $request) {
    	$data = $request->data;
    	$exam_id = $data['id'];
    	$user_id = $data['user_id'];
    	$score = $data['score'];
    	$rate = $data['rate'];
    	$createtime = Carbon::now();
    	$result = false;
        $exam='';
    	$result = ExamRecord::examRecordAdd($exam_id,$user_id,$rate,$score,$createtime);
        if($result)$exam=Exam::find($exam_id);
        // 这里其实就是两个平均分，先不改
    	return ['success'=>$result ? true:false,'exam'=>$exam];
    }

    //上传分数，更改总分
    public function totalUpdate(Request $request) {
        $data = $request->data;
        $exam_id = $data['id'];
        $total = $data['total'];
        $result = false;
        $result = Exam::totalUpdate($exam_id,$total);
        // 目前这里只是改一下总分，不需要回传
        // if($result)$exam=Exam::find($exam_id);
        return ['success'=>$result ? true:false];
    }
}
