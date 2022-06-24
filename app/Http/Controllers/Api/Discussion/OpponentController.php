<?php

namespace App\Http\Controllers\Api\Discussion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryDiscussion;
use App\Home\Encyclopedia\EntryDiscussion\EntryOpponent;
use App\Home\Publication\ArticleDiscussion\ArticleOpponent;
use App\Home\Examination\ExamDiscussion\ExamOpponent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OpponentController extends Controller
{
    //处理反对意见的接受机制
    public function oppose_accept(Request $request){
    	$id = $request->opponent_id;
    	$scope = $request->scope;
    	$result = false;
		$author_id = auth('api')->user() -> id;
        $author = auth('api')->user() -> username;
        $status = 1;
        $bc = '';
        if($scope==1){
        	$opponent = EntryOpponent::find($id);
			$recipient = $opponent->author;
			$recipient_id = $opponent->author_id;
			//接受了反对，更改反对意见为已接受，增加接受方
			$result = EntryOpponent::rejectAccept($id,$recipient_id,$recipient,$status);
            if($result){
                $bc = EntryOpponent::where([['eid',$opponent->eid],['pid',0]])->with('allOppose')->orderBy('created_at','DESC')->get();
            }
        }elseif ($scope==2) {
            $opponent = ArticleOpponent::find($id);
            $recipient = $opponent->author;
            $recipient_id = $opponent->author_id;
            $result = ArticleOpponent::rejectAccept($id,$recipient_id,$recipient,$status);
            if($result){
                $bc = ArticleOpponent::where([['aid',$opponent->aid],['pid',0]])->with('allOppose')->orderBy('created_at','DESC')->get();
            }
        }elseif ($scope==3) {
            $opponent = ExamOpponent::find($id);
            $recipient = $opponent->author;
            $recipient_id = $opponent->author_id;
            $result = ExamOpponent::rejectAccept($id,$recipient_id,$recipient,$status);
            if($result){
                $bc = ExamOpponent::where([['exam_id',$opponent->exam_id],['pid',0]])->with('allOppose')->orderBy('created_at','DESC')->get();
            }
        }
        
        return ['success'=>$result? true:false,'opposes'=>$bc];
	}

	//处理反对意见的拒绝机制
    public function oppose_reject(Request $request){
    	$id = $request->opponent_id;
    	$scope = $request->scope;
    	$result = false;
		$content_id = $request->id;
		$deadline = Carbon::now()->addDays(30);
		$title = $request->title;
		$comment = $request->reject;
		$author_id = auth('api')->user()->id;
        $author = auth('api')->user()->username;
        $bc = '';
        if($scope==1){
        	$opp = EntryOpponent::find($id);
			$recipient = $opp->author;
			$recipient_id = $opp->author_id;
			$round = $opp->round + 1;
	        if($title && $comment){
	            $result = EntryOpponent::rejectAdd($content_id,$deadline,$title,$comment,$id,$author_id,$author,$recipient_id,$recipient,$round);
                if($result){
                    $bc = EntryOpponent::where([['eid',$content_id],['pid',0]])->with('allOppose')->orderBy('created_at','DESC')->get();
                }
	        }
        }elseif ($scope==2) {
            $opp = ArticleOpponent::find($id);
            $recipient = $opp->author;
            $recipient_id = $opp->author_id;
            $round = $opp->round + 1;
            $result = ArticleOpponent::rejectAdd($content_id,$deadline,$title,$comment,$id,$author_id,$author,$recipient_id,$recipient,$round);
            if($result){
                $bc = ArticleOpponent::where([['aid',$content_id],['pid',0]])->with('allOppose')->orderBy('created_at','DESC')->get();
            }
        }elseif ($scope==3) {
            $opp = ExamOpponent::find($id);
            $recipient = $opp->author;
            $recipient_id = $opp->author_id;
            $round = $opp->round + 1;
            $result = ExamOpponent::rejectAdd($content_id,$deadline,$title,$comment,$id,$author_id,$author,$recipient_id,$recipient,$round);
            if($result){
                $bc = ExamOpponent::where([['aid',$content_id],['pid',0]])->with('allOppose')->orderBy('created_at','DESC')->get();
            }
        }
        
    	return ['success'=>$result? true:false,'opposes'=>$bc];
    }
}
