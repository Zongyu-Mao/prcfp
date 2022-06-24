<?php

namespace App\Http\Controllers\Api\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationEvent;
use App\Home\Encyclopedia\EntryReview\EntryReviewEvent;
use App\Home\Encyclopedia\EntryResort\EntryResortEvent;
use App\Home\Encyclopedia\EntryDiscussion\EntryDiscussionEvent;
use App\Home\Encyclopedia\EntryDebate\EntryDebateEvent;
use App\Home\Encyclopedia\EntryDynamic;
use App\Home\Publication\ArticleCooperation\ArticleCooperationEvent;
use App\Home\Publication\ArticleReview\ArticleReviewEvent;
use App\Home\Publication\ArticleResort\ArticleResortEvent;
use App\Home\Publication\ArticleDiscussion\ArticleDiscussionEvent;
use App\Home\Publication\ArticleDebate\ArticleDebateEvent;
use App\Home\Publication\Article\ArticleDynamic;
use App\Home\Examination\ExamCooperation\ExamCooperationEvent;
use App\Home\Examination\ExamReview\ExamReviewEvent;
use App\Home\Examination\ExamResort\ExamResortEvent;
use App\Home\Examination\ExamDiscussion\ExamDiscussionEvent;
use App\Home\Examination\ExamDebate\ExamDebateEvent;
use App\Home\Examination\Exam\ExamDynamic;


class EventController extends Controller
{
    //
    public function getEvents(Request $request) {
    	$data = $request->data;
    	$scope = $data['scope'];
    	$beyond = $data['beyond'];
    	$id = $data['id'];
    	$events = '';
    	if($scope===1){
    		switch($beyond){
    			case 1:
    				$events = EntryCooperationEvent::where('cooperation_id',$id)->orderBy('created_at','desc')->limit(15)->get();
    			break;
    			case 2:
    				$events = EntryReviewEvent::where('rid',$id)->orderBy('created_at','desc')->limit(15)->get();
    			break;
    			case 3:
    				$events = EntryResortEvent::where('eid',$id)->orderBy('created_at','desc')->limit(15)->get();
    			break;
    			case 4:
    				$events = EntryDiscussionEvent::where('eid',$id)->orderBy('created_at','DESC')->limit(15)->get();
    			break;
    			case 5:
    				$events = EntryDebateEvent::where('debate_id',$id)->orderBy('created_at','desc')->limit(15)->get();
    			break;
    			default:
    				$events = EntryDynamic::where('eid',$id)->orderBy('createtime','DESC')->limit(15)->get();
    			break;
    		}
    	}else if($scope===2){
    		switch($beyond){
    			case 1:
    				$events = ArticleCooperationEvent::where('cooperation_id',$id)->orderBy('created_at','desc')->limit(15)->get();
    			break;
    			case 2:
    				$events = ArticleReviewEvent::where('rid',$id)->orderBy('created_at','desc')->limit(15)->get();
    			break;
    			case 3:
    				$events = ArticleResortEvent::where('aid',$id)->orderBy('created_at','desc')->limit(15)->get();
    			break;
    			case 4:
    				$events = ArticleDiscussionEvent::where('aid',$id)->orderBy('created_at','DESC')->limit(15)->get();
    			break;
    			case 5:
    				$events = ArticleDebateEvent::where('debate_id',$id)->orderBy('created_at','desc')->limit(15)->get();
    			break;
    			default:
    				$events = ArticleDynamic::where('aid',$id)->orderBy('createtime','DESC')->limit(15)->get();
    			break;
    		}
    	}else if($scope===3){
    		switch($beyond){
    			case 1:
    				$events = ExamCooperationEvent::where('cooperation_id',$id)->orderBy('created_at','desc')->limit(15)->get();
    			break;
    			case 2:
    				$events = ExamReviewEvent::where('rid',$id)->orderBy('created_at','desc')->limit(15)->get();
    			break;
    			case 3:
    				$events = ExamResortEvent::where('exam_id',$id)->orderBy('created_at','desc')->limit(15)->get();
    			break;
    			case 4:
    				$events = ExamDiscussionEvent::where('exam_id',$id)->orderBy('created_at','DESC')->limit(15)->get();
    			break;
    			case 5:
    				$events = ExamDebateEvent::where('debate_id',$id)->orderBy('created_at','desc')->limit(15)->get();
    			break;
    			default:
    				$events = ExamDynamic::where('exam_id',$id)->orderBy('createtime','DESC')->limit(15)->get();
    			break;
    		}
    	}
    	return ['events'=>$events];
    }
}
