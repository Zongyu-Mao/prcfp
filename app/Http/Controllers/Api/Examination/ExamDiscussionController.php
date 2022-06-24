<?php

namespace App\Http\Controllers\Api\Examination;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Examination\ExamDiscussion;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\ExamDiscussion\ExamOpponent;
use App\Home\Examination\ExamDiscussion\ExamAdvise;
use App\Home\Examination\ExamDiscussion\ExamDiscussionEvent;
use App\Home\Examination\Exam;
use App\User;
use Carbon\Carbon;
use JWTAuth;

class ExamDiscussionController extends Controller
{
    //首页展示
    public function examDiscussion(Request $request,$id,$title){
    	
    	$exam = Exam::find($id);
    	//如果请求的id存在
    	if($id && $exam){
	    	//查看该词条下是否有讨论内容
	    	$discuss_count = ExamDiscussion::where('exam_id',$id)->exists() ? '1':'0';
	    	$oppose_count = ExamOpponent::where('exam_id',$id)->exists() ? '1':'0';
	    	$advise_count = ExamAdvise::where('exam_id',$id)->exists() ? '1':'0';
	    	//判断评审中是否存在协作计划，如果存在协作计划，接收反对的选项应对协作小组可见，否则，对自管理员可见
	    	$coo_count = ExamCooperation::where([['exam_id',$id],['status','0']])->exists() ? '1':'0';
            $cooperation = ExamCooperation::where([['exam_id',$id],['status','0']])->first();
	    	if($coo_count){
	    		//如果存在活跃的协作计划，取得协作计划成员组
	    		$initiate_id = $cooperation->manage_id;
                $array_encoo_crew_ids = $cooperation->crews()->pluck('user_id')->toArray();
	    		array_push($array_encoo_crew_ids, $initiate_id);
	    	}else{
	    		//如果没有活跃的协作计划，评审由自管理员托管
	    		$initiate_id = Exam::where('id',$id)->first()->manager_id;
	    		$array_encoo_crew_ids = array();
	    	}
				$manager_id = Exam::where('id',$id)->first()->manager_id;
			//取得反对内容
			if($oppose_count){
				$data_oppose = ExamOpponent::where([['exam_id',$id],['pid',0]])->with('allOppose')->orderBy('created_at','DESC')->get();
			}else{
				$data_oppose = '';
			}
			//取得建议内容
			if($advise_count){
				$data_advise = ExamAdvise::where([['exam_id',$id],['pid',0]])->with('allAdvise')->get();
			}else{
				$data_advise = '';
			}
			//取得普通讨论内容
			if($discuss_count){
				$data_discuss = ExamDiscussion::where([['exam_id',$id],['pid',0]])->with('allDiscuss')->get();
			}else{
				$data_discuss = '';
			}
			//取得词条讨论的事件内容
			$discuss_event_count = ExamDiscussionEvent::where('exam_id',$id)->exists() ? '1':'0';;
			if($discuss_event_count){
				$data_events = ExamDiscussionEvent::where('exam_id',$id)->orderBy('created_at','DESC')->get();
			}else{
				$data_events = '';
			}
	    	$data = array(
	    		'basic'		=> $exam,
	    		'crews'		=> $array_encoo_crew_ids,
	    		'opposes'	=> $data_oppose,
	    		'advises'	=> $data_advise,
	    		'discussions'	=> $data_discuss,
	    		'events'		=> $data_events
	    	);
    	}
    	return $data;
    }
}
