<?php

namespace App\Http\Controllers\Api\Personal\MyWorks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\ExamResort;
use App\Home\Examination\ExamReview;
use App\Home\Examination\ExamDiscussion\ExamOpponent;
use App\Home\Examination\ExamDiscussion\ExamAdvise;
use App\Home\Examination\ExamDebate;
use App\Home\Examination\ExamCooperation\ExamCooperationUser;

class myExamController extends Controller
{
    //展示我的试卷
    public function myExams(Request $request){
        $user = auth('api')->user();
    	$user_id = $user->id;
    	// 我的自管理词条
    	$manageExams = Exam::where('manage_id',$user_id)->orderBy('created_at','desc')->get();
        $manageCooeprationIds = array_filter($manageExams->pluck('cooperation_id')->toArray());
        // 我的自管理协作计划
        $manageCooperations = ExamCooperation::whereIn('id',$manageCooeprationIds)->with('getExam')->orderBy('created_at','desc')->get();
        // 我的普通协作
        $cooperationIds = ExamCooperationUser::where('user_id',$user_id)->pluck('cooperation_id')->toArray();
        $normalCooperations = ExamCooperation::whereIn('id',$cooperationIds)->with('getExam')->orderBy('created_at','desc')->get();
        // 我的求助
        $myResorts = ExamResort::where([['author_id',$user_id],['pid',0]])->with('getContent')->orderBy('created_at','desc')->get();
        // 我的评审
        $myReviews = ExamReview::where('initiate_id',$user_id)->with('getExam')->orderBy('created_at','desc')->get();
        
        // 我的攻辩
        $myDebates = ExamDebate::where('Aauthor_id',$user_id)->orWhere('Bauthor_id',$user_id)->orWhere('referee_id',$user_id)->with('getExam')->orderBy('created_at','desc')->get();

        // 我的反对
        $myOpponents = ExamOpponent::where('author_id',$user_id)->orWhere('recipient_id',$user_id)->with('getExam')->orderBy('created_at','desc')->get();
        // 我的建议
        $myAdvises = ExamAdvise::where('author_id',$user_id)->orWhere('recipient_id',$user_id)->with('getExam')->orderBy('created_at','desc')->get();
        
        return $data = array(
        	'exams' => $manageExams,
        	'm_cooperations' => $manageCooperations,
        	'n_cooperations' => $normalCooperations,
        	'resorts' => $myResorts,
        	'reviews' => $myReviews,
        	'debates' => $myDebates,
        	'opponents' => $myOpponents,
        	'advises' => $myAdvises
        );
    }
}
