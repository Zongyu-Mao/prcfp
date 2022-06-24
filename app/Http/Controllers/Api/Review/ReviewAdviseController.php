<?php

namespace App\Http\Controllers\Api\Review;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryReview\EntryReviewAdvise;
use App\Home\Encyclopedia\EntryReview\EntryReviewEvent;
use App\Home\Encyclopedia\EntryReview\EntryReviewDiscussion;
use App\Home\Publication\ArticleReview\ArticleReviewAdvise;
use App\Home\Publication\ArticleReview\ArticleReviewEvent;
use App\Home\Publication\ArticleReview\ArticleReviewDiscussion;
use App\Home\Examination\ExamReview\ExamReviewAdvise;
use App\Home\Examination\ExamReview\ExamReviewEvent;
use App\Home\Examination\ExamReview\ExamReviewDiscussion;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

class ReviewAdviseController extends Controller
{
    //处理建议内容区
    public function advise(Request $request){
		$id = $request->rid;
		$scope = $request->scope;
		$result = false;
		// $deadline = Carbon::now()->addDays(30);
		$title = $request->title;
		$comment = $request->advise;
        $user = auth('api')->user()->only('id','username');
		$author_id = $user['id'];
        $author = $user['username'];
        $as = '';
        // return $request;
        if($title && $comment){
            if($scope==1){
               $result = EntryReviewAdvise::reviewAdvisementCreate($id,$title,$comment,$author_id,$author); 
               if($result)$as = EntryReviewAdvise::where([['rid',$id],['pid',0]])->orderBy('created_at','desc')->with('allAdvise')->get();
           }elseif($scope==2){
                $result = ArticleReviewAdvise::reviewAdvisementCreate($id,$title,$comment,$author_id,$author);
                if($result)$as = ArticleReviewAdvise::where([['rid',$id],['pid',0]])->orderBy('created_at','desc')->with('allAdvise')->get();
           }elseif($scope==3){
                $result = ExamReviewAdvise::reviewAdvisementCreate($id,$title,$comment,$author_id,$author);
                if($result)$as = ExamReviewAdvise::where([['rid',$id],['pid',0]])->orderBy('created_at','desc')->with('allAdvise')->get();
           }
            
        }
        return ['success'=>$result? true:false,'advises'=>$as];
    }

    //处理建议的接受机制
    public function advise_accept(Request $request){
   		$id = $request->advise_id;
   		$scope = $request->scope;
   		$result = false;
		$user = auth('api')->user()->only('id','username');
        $author_id = $user['id'];
        $author = $user['username'];
        $status = 1;
        $as = '';
		//接受了建议，更改建议状态为已接受，增加接受方
        if($scope==1){
            $rid = EntryReviewAdvise::find($id)->rid;
            $result = EntryReviewAdvise::reviewAdvisementAccept($id,$author_id,$author,$status);
            if($result)$as = EntryReviewAdvise::where([['rid',$rid],['pid',0]])->orderBy('created_at','desc')->with('allAdvise')->get();
        }elseif($scope==2){
            $rid = ArticleReviewAdvise::find($id)->rid;
            $result = ArticleReviewAdvise::reviewAdvisementAccept($id,$author_id,$author,$status);
            if($result)$as = EntryReviewAdvise::where([['rid',$rid],['pid',0]])->orderBy('created_at','desc')->with('allAdvise')->get();
        }elseif($scope==3){
            $rid = ExamReviewAdvise::find($id)->rid;
            $result = ExamReviewAdvise::reviewAdvisementAccept($id,$author_id,$author,$status);
            if($result)$as = ExamReviewAdvise::where([['rid',$rid],['pid',0]])->orderBy('created_at','desc')->with('allAdvise')->get();
        }
		

        return ['success'=>$result? true:false,'advises'=>$as];
    }

    //处理建议的拒绝机制
    public function advise_reject(Request $request){
    	$id = $request->advise_id;
    	$scope = $request->scope;
   		$result = false;
		$title = $request->title;
		$comment = $request->reject;
		$user = auth('api')->user()->only('id','username');
        $author_id = $user['id'];
        $author = $user['username'];
        $as = '';
        if($title && $comment){
            if($scope==1){
                $advise = EntryReviewAdvise::find($id);
                $rid = $advise->rid;
                $recipient = $advise->author;
                $recipient_id = $advise->author_id;
                $round = $advise->round;
                $result = EntryReviewAdvise::reviewAdvisementReject($rid,$title,$comment,$id,$author_id,$author,$recipient_id,$recipient);
                if($result)$as = EntryReviewAdvise::where([['rid',$rid],['pid',0]])->orderBy('created_at','desc')->with('allAdvise')->get();
            }elseif($scope==2){
                
                $advise = ArticleReviewAdvise::find($id);
                $rid = $advise->rid;
                $recipient = $advise->author;
                $recipient_id = $advise->author_id;
                $round = $advise->round;
                $result = ArticleReviewAdvise::reviewAdvisementReject($rid,$title,$comment,$id,$author_id,$author,$recipient_id,$recipient);
                if($result)$as = ArticleReviewAdvise::where([['rid',$rid],['pid',0]])->orderBy('created_at','desc')->with('allAdvise')->get();
            }elseif($scope==3){
                
                $advise = ExamReviewAdvise::find($id);
                $rid = $advise->rid;
                $recipient = $advise->author;
                $recipient_id = $advise->author_id;
                $round = $advise->round;
                $result = ExamReviewAdvise::reviewAdvisementReject($rid,$title,$comment,$id,$author_id,$author,$recipient_id,$recipient);
                if($result)$as = ExamReviewAdvise::where([['rid',$rid],['pid',0]])->orderBy('created_at','desc')->with('allAdvise')->get();
            }
            
        }
        return ['success'=>$result? true:false,'advises'=>$as];
    	
    }
}
