<?php

namespace App\Http\Controllers\Api\Examination;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Examination\Exam;
use App\Home\Examination\Exam\ExamDynamic;

class ExamHistoryController extends Controller
{
    //显示著作的历史（动态）
    public function examHistory(Request $requst,$id,$title){
    	$exam = Exam::find($id);
    	$dynamics = ExamDynamic::where('exam_id',$id)->orderBy('createtime','DESC')->limit(20)->get();
    	return $dynamics;
    }
}
