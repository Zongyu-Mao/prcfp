<?php

namespace App\Http\Controllers\Api\Examination;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Examination\ExamDebate;
use App\Home\Examination\ExamReview;
use App\Home\Examination\ExamDebate\ExamDebateEvent;
use App\Home\Examination\ExamReview\ExamReviewEvent;
use App\Home\Examination\ExamDebate\ExamDebateComment;
use App\Home\Examination\ExamDiscussion\ExamOpponent;
use App\Home\Examination\ExamDiscussion\ExamDiscussionEvent;
use App\Home\Examination\ExamReview\ExamReviewOpponent;
use App\Home\Examination\Exam;
use App\User;
use Carbon\Carbon;
use JWTAuth;

class ExamDebateController extends Controller
{
    //首页显示
    public function examDebate(Request $request,$id,$title){
    	$exam = Exam::find($id);
    	$type = $request->type;
    	$typeID = $request->type_id;
    	$data_debateAll = '';
    	$data_comments = '';
    	$starRecord = [];
    	$debateFrom = '';
    	if($id && $title==$exam->title){
    		// 取得debate数据,否则为空
    		if(ExamDebate::where('exam_id',$id)->exists()){
    			$data_debateAll = ExamDebate::where('exam_id',$id)->orderBy('created_at','DESC')->get();
    		}
    	}
    	return $data = array(
    		'debate_all'	=> $data_debateAll,
    	);
    }

    //单debate的详情
    public function debate(Request $request){
    	$id = $request->id;
    	$type = $request->type;
    	$typeID = $request->type_id;
    	$data_comments = '';
    	$starRecords = [];
    	$debateFrom = '';
    	// return $request;
    	// return ExamDebate::where('eid',$id)->get();
    	// return ExamDebate::where([['eid',$id],['type',$type],['type_id',$typeID]])->first();;
    	if($type && $typeID){
			// 这里得到具体的debate了
			$debate = ExamDebate::where([['exam_id',$id],['type',$type],['type_id',$typeID]])->with('getStars')->first();
            if($debate){
                $debate_id = $debate->id;
                // $starRecord = $debate->getStars();
                // $starRecord = $debate->getStars->pluck('user_id')->toArray();
                // array_push($starRecord,$debate->Aauthor_id);
                // array_push($starRecord,$debate->Bauthor_id);
                // if($debate->referee_id){
                // 	array_push($starRecord,$debate->referee_id);
                // };
                // $starRecords = array_unique($starRecord);
                //判断网友留言是否存在
                if(ExamDebateComment::where([['exam_id',$id],['debate_id',$debate_id],['pid',0]])->exists()){
					$data_comments = ExamDebateComment::where([['exam_id',$id],['debate_id',$debate_id],['pid',0]])->with('allComment')->orderBy('created_at','DESC')->get();
				}
    			
    		}
			// dd($debate);
			if($type == 1){
				$debateFrom = ExamReviewOpponent::find($typeID)->title;
			}elseif($type == 2){
				$debateFrom = ExamOpponent::find($typeID)->title;
			}
	    	
    	}
    	return $data = array(
    		'debate'		=> $debate,
    		'comments'		=> $data_comments,
    		'debateFrom'	=> $debateFrom
    		);
    	
    }
}
