<?php

namespace App\Http\Controllers\Api\Examination;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\Examination\ExamCooperation\ExamCooperationAssignModifiedEvent;
use App\Home\Examination\ExamCooperation\ExamCooperationDiscussion;
use App\Home\Examination\ExamCooperation\ExamCooperationMessage;
use App\Home\Examination\ExamCooperation\ExamCooperationEvent;
use App\Home\Examination\ExamCooperation\ExamCooperationVote;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\Exam;
use App\Home\Classification;
use App\Home\UserDynamic;
use App\Models\User;

class ExamCooperationController extends Controller
{
    //展示首页
    public function examCooperation(Request $request,$id,$examTitle){
        $exam = Exam::where([['id',$id],['title',$examTitle]])->first();       
        if($exam && $exam->cooperation_id){
        	$cooperation = ExamCooperation::where('id',$exam->cooperation_id)->with('crews')->with('contributions')->first();
	        $cooperationId = $cooperation->id;
	    	$data_class = Classification::getClassPath($cooperation->cid);
	        
	        //判断讨论表中是否有关于本词条协作计划的讨论，如果有，取出，如果没有，返回空
	        $discussion = ExamCooperationDiscussion::where('cooperation_id',$cooperationId)->get();
	        //读取协作投票信息
            $data_votes = ExamCooperationVote::where([['cooperation_id',$cooperationId],['status','0']])->orderBy('created_at','desc')->with('getVoteRecord')->get();
            $history_votes=ExamCooperationVote::where([['cooperation_id',$cooperationId],['status','>','0']])->orderBy('created_at','desc')->with('getVoteRecord')->get();
            $crewArr = $cooperation->crews()->pluck('user_id')->toArray();
            array_push($crewArr,$exam->manage_id);
            // dd($data_votes);
            $userDynamics = UserDynamic::whereIn('user_id',$crewArr)->orderBy('createtime','desc')->limit('30')->get();
            // dd($data_votes);

	        //读取协作计划的事件和动态
            $data_events = ExamCooperationEvent::where('cooperation_id',$cooperationId)->orderBy('created_at','desc')->get();
	        
	        //读取协作计划面板的用户留言
            $data_message = ExamCooperationMessage::where([['cooperation_id',$cooperationId],['pid','0']])->with('reply')->get();
            $data_reply = ExamCooperationMessage::where([['cooperation_id',$cooperationId],['pid','!=','0']])->get();

	        $crews = User::whereIn('id',$crewArr)->with('getAvatar')->get();
	        $return = array(
	            'basic'       => $exam,
	            'cooperation'   => $cooperation,
	            'crews'   		=> $crews,
	            'crewArr'   	=> $crewArr,
	            'data_class'    => $data_class,
	            'discussion' 	=> $discussion,
	            'votes'   		=> $data_votes,
	            'history_votes' => $history_votes,
	            'data_events' 	=> $data_events,
	            'userDynamics' 	=> $userDynamics,
	            'data_message' 	=> $data_message,
	            'data_reply' 	=> $data_reply
	        );
	    	
	    }else {
	    	$return = array(
	            'exam'       => $exam
	        );
	    }
	    return $return;
    }
}
