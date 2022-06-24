<?php

namespace App\Http\Controllers\Api\Examination\ExamReview;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamReview;
use App\Home\Examination\ExamDiscussion\ExamOpponent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ExamReviewCreateController extends Controller
{
    //评审计划的创建
    public function create(Request $request){
    	$id = $request->id;
    	$input = $request->input;
    	$result = false;
    	$exam = Exam::where('id',$id)->first();
        $level = $exam->level;
    	$kpi_count = ExamReview::where([['exam_id',$id],['status','0']])->exists() ? '1':'0';
    	//判断是否存在词条的反对意见
    	$exam_oppose_count = ExamOpponent::where([['exam_id',$id],['status','0']])->exists() ? '1':'0';
    	if($request->isMethod('post') && $kpi_count == '0'){
            //接收留言内容并写入数据表 
            $target = $input['target'];
            $cid = $input['cid'];
            $timelimit = $input['timelimit'];
            $deadline = Carbon::now()->addDays($timelimit*15);
            $title = $input['title'];
            $content = $input['content'];
            $initiate_id =auth('api')->user()->id;
            $initiate = auth('api')->user()->username;
            $examTitle = $exam->title;
            // return $data1;
            if($title && $content && $target == $level+1){
                // 创建评审计划
                $result = ExamReview::reviewCreate($id,$target,$cid,$deadline,$title,$content,$initiate_id,$initiate,$examTitle);
                //发表了有效的讨论后，积分和成长值+5
                $result1 = User::expAndGrowValue($initiate_id,100,100);
                Exam::where('id',$id)->update(['review_id' => $result]);
            }
        }
        return ['success'=>$result? true:false];	
    }
}
