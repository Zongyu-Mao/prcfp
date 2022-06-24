<?php

namespace App\Http\Controllers\Api\Examination;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\ExamResort;
use App\Home\Examination\ExamResort\ExamResortSupportComment;
use App\Home\Examination\ExamResort\ExamResortEvent;
use App\Home\Examination\Exam;
use Carbon\Carbon;
use App\User;
use JWTAuth;

class ExamResortController extends Controller
{
    //展示页
	public function examResort(Request $request,$id,$name){
		
		$exam = Exam::find($id)->only('id','title','cid','manage_id');
		if($id && $exam){
			$etitle = $exam['title'];
			$asking_count = ExamResort::where([['exam_id',$id],['pid','0']])->exists() ? '1':'0';
			$helper_count = ExamResort::where([['exam_id',$id],['pid','!=','0']])->exists() ? '1':'0';
			//判断评审中是否存在协作计划，如果存在协作计划，接收反对的选项应对协作小组可见，否则，对自管理员可见
	    	$cooperationCount = ExamCooperation::where([['exam_id',$id],['status','0']])->exists() ? '1':'0';
            $cooperation = ExamCooperation::where([['exam_id',$id],['status','0']])->first();
	    	$manage_id = $exam['manage_id'];
	    	if($cooperationCount)$array_encoo_crew_ids = $cooperation->crews()->pluck('user_id')->toArray();
	    	array_push($array_encoo_crew_ids, $manage_id);
	    	//如果存在求助话题
    		if($asking_count){
    			$data_asking = ExamResort::where([['exam_id',$id],['pid','0']])->with('helpers')->orderBy('created_at','DESC')->get();
    		}else{
    			$data_asking = '';
    		}
            // dd($data_asking);

    		if($helper_count){
    			$data_helper = ExamResort::where([['exam_id',$id],['pid','!=','0']])->orderBy('created_at','DESC')->get();
    		}else{
    			$data_helper = '';
    		}
    		$helper_comment_count = ExamResortSupportComment::where([['exam_id',$id],['type','0']])->exists() ? '1':'0';
    		if($helper_comment_count){
    			$helper_comment = ExamResortSupportComment::where([['exam_id',$id],['type','0']])->with('allComment')->orderBy('created_at','DESC')->get();
    		}else{
    			$helper_comment = '';
    		}
    		
    		$help_reject_count = ExamResortSupportComment::where([['exam_id',$id],['type','1']])->exists() ? '1':'0';
    		if($help_reject_count){
    			$help_reject = ExamResortSupportComment::where([['exam_id',$id],['type','1']])->orderBy('created_at','DESC')->get();
    		}else{
    			$help_reject = '';
    		}
    		$events = ExamResortEvent::where('exam_id',$id)->orderBy('created_at','desc')->limit(15)->get();
	    	$data = array(
	    		'basic'		=> $exam,
	    		'manage_id'		=> $manage_id,
	    		'asking_count'		=> $asking_count,
	    		'helper_count'		=> $helper_count,
	    		'help_reject_count'		=> $help_reject_count,
	    		'crews'		=> $array_encoo_crew_ids,
	    		'helper_comment_count'		=> $helper_comment_count,
	    		'resorts' => $data_asking,
	    		'helpers' => $data_helper,
	    		'events' => $events,
	    		'helper_comments' => $helper_comment,
	    		'help_rejects' => $help_reject,

	    	);
		}
		return $data;
	}
}
