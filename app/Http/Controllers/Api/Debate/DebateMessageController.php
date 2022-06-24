<?php

namespace App\Http\Controllers\Api\Debate;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryDebate;
use App\Home\Encyclopedia\EntryDebate\EntryDebateEvent;
use App\Home\Encyclopedia\EntryDebate\EntryDebateComment;
use App\Home\Publication\ArticleDebate\ArticleDebateComment;
use App\Home\Examination\ExamDebate\ExamDebateComment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DebateMessageController extends Controller
{
    //处理辩论留言信息
    public function debate_message(Request $request){
    	$debate_id = $request->debate_id;
    	$id = $request->id;
    	$scope = $request->scope;
    	$result = false;
    	// return $request;
		$author_id = auth('api')->user()->id;
		$author = auth('api')->user()->username;    		
        $title = $request->title;
		$comment = $request->message;
		$pid = '0';
		$type = $request->standPoint;
        $comments = '';
		if($scope==1){
			$result = EntryDebateComment::commentAdd($id,$debate_id,$comment,$pid,$author_id,$author,$title,$type);
            if($result) {
                $comments = EntryDebateComment::where([['eid',$id],['debate_id',$debate_id],['pid',0]])->with('allComment')->orderBy('created_at','DESC')->get();
            }
		}elseif ($scope==2) {
            $result = ArticleDebateComment::commentAdd($id,$debate_id,$comment,$pid,$author_id,$author,$title,$type);
            if($result) {
                $comments = ArticleDebateComment::where([['aid',$id],['debate_id',$debate_id],['pid',0]])->with('allComment')->orderBy('created_at','DESC')->get();
            }
        }elseif ($scope==3) {
            $result = ExamDebateComment::commentAdd($id,$debate_id,$comment,$pid,$author_id,$author,$title,$type);
            if($result) {
                $comments = ExamDebateComment::where([['exam_id',$id],['debate_id',$debate_id],['pid',0]])->with('allComment')->orderBy('created_at','DESC')->get();
            }
        }
		
        return ['success'=>$result ? true:false,'comments'=>$comments];

    }

    //处理辩论留言的回复
    public function debate_message_reply(Request $request){
    	$id = $request->message_id;
    	$debate_id = $request->debate_id;
    	$content_id = $request->id;
    	$scope = $request->scope;
		$author_id = auth('api')->user()->id;
		$author = auth('api')->user()->username;
        $title = $request->title;
		$comment = $request->reply;
        // return $request;
		if($scope==1){
			$result = EntryDebateComment::commentAdd($content_id,$debate_id,$comment,$id,$author_id,$author,$title,'0');
            if($result) {
                $comments = EntryDebateComment::where([['eid',$content_id],['debate_id',$debate_id],['pid',0]])->with('allComment')->orderBy('created_at','DESC')->get();
            }
		}elseif ($scope==2) {
            $result = ArticleDebateComment::commentAdd($content_id,$debate_id,$comment,$id,$author_id,$author,$title,'0');
            if($result) {
                $comments = ArticleDebateComment::where([['aid',$content_id],['debate_id',$debate_id],['pid',0]])->with('allComment')->orderBy('created_at','DESC')->get();
            }
        }elseif ($scope==3) {
            $result = ExamDebateComment::commentAdd($content_id,$debate_id,$comment,$id,$author_id,$author,$title,'0');
            if($result) {
                $comments = ExamDebateComment::where([['exam_id',$content_id],['debate_id',$debate_id],['pid',0]])->with('allComment')->orderBy('created_at','DESC')->get();
            }
        }
    	return ['success'=>$result ? true:false,'comments'=>$comments];
    }
}
