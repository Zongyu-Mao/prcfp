<?php

namespace App\Http\Controllers\Api\Examination;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Examination\ExamReview;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\ExamReview\ExamReviewOpponent;
use App\Home\Examination\ExamReview\ExamReviewAdvise;
use App\Home\Examination\ExamReview\ExamReviewEvent;
use App\Home\Examination\ExamReview\ExamReviewDiscussion;
use App\Home\Examination\ExamReview\ExamReviewRecord;
use App\Home\Examination\ExamDiscussion\ExamOpponent;
use App\Home\Examination\ExamDebate;
use App\Home\Examination\Exam;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

class ExamReviewController extends Controller
{
    //评审计划首页
    public function examReview(Request $request,$id,$title){
        // 评审只有在协作计划活跃且词条反对均处理掉的情况下才能开启,为了避免冲突，协作计划的结束时间应该大于评审结束时间
        $exam = Exam::find($id);
        $manage_id = $exam->manage_id;
        $cooperation = ExamCooperation::where([['exam_id',$id],['status','0']])->first();
        $review = ExamReview::where([['exam_id',$id],['status','0']])->with('getReviewRecord')->first();
        $active_oppose_count = ExamOpponent::where([['exam_id',$id],['status','0']])->count();
        $active_debate_count = ExamDebate::where([['exam_id',$id],['status','0']])->count();
        if($review){
            $rid = $review->id;
            $data_kpi = ExamReview::where([['exam_id',$id],['status','0']])->get();
            //如果存在与本评审计划相关的反对意见，获取数据
            $reviewOpponents = ExamReviewOpponent::where([['rid',$rid],['pid',0]])->with('allOppose')->get();
            //如果存在与本评审计划相关的反对意见，获取数据
            $reviewAdvises = ExamReviewAdvise::where([['rid',$rid],['pid',0]])->with('allAdvise')->get();;

            $reviewDiscussions = ExamReviewDiscussion::where([['rid',$rid],['pid',0]])->with('allDiscuss')->with('getAuthor')->get();;

            $reviewEvents = ExamReviewEvent::where('rid',$rid)->orderBy('created_at','desc')->limit(10)->get();;
            // dd($rid);
            // dd($reviewEvents);

            $reviewRecord = ExamReviewRecord::where('review_id',$rid)->get();
            $agreeNum = ExamReviewRecord::getAgreeNum($rid);
            $opposeNum = ExamReviewRecord::getOpposeNum($rid);
            $neutralNum = ExamReviewRecord::getNeutralNum($rid);
            $reviewArr = $reviewRecord->pluck('user_id')->toArray();
            $myReview = $reviewRecord->filter(function($item){
                return $item->user_id==auth('api')->user()->id; 
            });
            $myReview = $myReview->first();
        }else{
            $data_kpi = null;
            //如果存在与本评审计划相关的反对意见，获取数据
            $reviewOpponents = null;
            //如果存在与本评审计划相关的反对意见，获取数据
            $reviewAdvises = null;

            $reviewDiscussions = null;

            $reviewEvents = null;

            $reviewRecord = null;
            $agreeNum = null;
            $opposeNum = null;
            $neutralNum = null;
            $reviewArr = null;
            $myReview = null;
        }


    	$examTitle = $exam->title;
    	$examLevel = $exam->level;
    	$cooperationCrews = [];
        if($cooperation)$cooperationCrews = $cooperation->crews()->pluck('user_id')->toArray();
        array_push($cooperationCrews, $manage_id);
    	
    	return $res = [
    		'basic' 	=>	$exam,
    		'review'	=>	$review,
    		'opponents'	=>	$reviewOpponents,
    		'advises'	=>	$reviewAdvises,
    		'discussions'	=>	$reviewDiscussions,
    		'events'	=>	$reviewEvents,
    		'reviewArr'	=>	$reviewArr,
    		'myReview'	=>	$myReview,
    		'agreeNum'      => $agreeNum,
            'opposeNum'      => $opposeNum,
    		'neutralNum'		=> $neutralNum,
    		'cooperationCrews'	=> $cooperationCrews,
    		'active_oppose_count'	=> $active_oppose_count,
            'active_debate_count'   => $active_debate_count

    	];
    }
}
