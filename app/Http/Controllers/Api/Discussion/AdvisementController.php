<?php

namespace App\Http\Controllers\Api\Discussion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryDiscussion\EntryAdvise;
use App\Home\Publication\ArticleDiscussion\ArticleAdvise;
use App\Home\Examination\ExamDiscussion\ExamAdvise;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AdvisementController extends Controller
{
    //处理建议的拒绝机制
    public function advise_reject(Request $request){
    	$id = $request->advise_id;
    	$content_id = $request->id;
    	$scope = $request->scope;
    	$result = false;
		$deadline = Carbon::now()->addDays(30);
		$title = $request->title;
		$comment = $request->reject;
		$author_id = auth('api')->user()->id;
        $author = auth('api')->user()->username;
        $bc = '';
        if($title && $comment){
        	if($scope==1){
                $advise = EntryAdvise::find($id);
                $recipient = $advise->author;
                $recipient_id = $advise->author_id;
                $round = $advise->round + 1;
        		$result = EntryAdvise::rejectAdd($content_id,$deadline,$title,$comment,$id,$author_id,$author,$recipient_id,$recipient,$round);
                if($result){
                    $bc = EntryAdvise::where([['eid',$content_id],['pid',0]])->orderBy('created_at','DESC')->with('allAdvise')->get();
                }
        	}elseif ($scope==2) {
                $advise = ArticleAdvise::find($id);
                $recipient = $advise->author;
                $recipient_id = $advise->author_id;
                $round = $advise->round + 1;
                $result = ArticleAdvise::rejectAdd($content_id,$deadline,$title,$comment,$id,$author_id,$author,$recipient_id,$recipient,$round);
                if($result){
                    $bc = ArticleAdvise::where([['aid',$content_id],['pid',0]])->orderBy('created_at','DESC')->with('allAdvise')->get();
                }
            }elseif ($scope==3) {
                $advise = ExamAdvise::find($id);
                $recipient = $advise->author;
                $recipient_id = $advise->author_id;
                $round = $advise->round + 1;
                $result = ExamAdvise::rejectAdd($content_id,$deadline,$title,$comment,$id,$author_id,$author,$recipient_id,$recipient,$round);
                if($result){
                    $bc = ArticleAdvise::where([['exam_id',$content_id],['pid',0]])->orderBy('created_at','DESC')->with('allAdvise')->get();
                }
            }
            
        }
	
    	return ['success' => $result? true:false,'advises'=>$bc];
    }

    //处理建议的接受机制
    public function advise_accept(Request $request){
    	$id = $request->advise_id;
    	$scope = $request->scope;
    	$result = false;
		$author_id = auth('api')->user()->id;
        $author = auth('api')->user()->username;
        $status = 1;
        $bc = '';
		//接受了反对，更改反对意见为已接受，增加接受方
		if($scope==1){
            $content_id = EntryAdvise::find($id)->eid;
			$result = EntryAdvise::adviseAccept($id,$author_id,$author,$status);   
            if($result){
                $bc = EntryAdvise::where([['eid',$content_id],['pid',0]])->orderBy('created_at','DESC')->with('allAdvise')->get();
            }
		}elseif ($scope==2) {
            $content_id = ArticleAdvise::find($id)->aid;
            $result = ArticleAdvise::adviseAccept($id,$author_id,$author,$status);  
            if($result){
                $bc = ArticleAdvise::where([['aid',$content_id],['pid',0]])->orderBy('created_at','DESC')->with('allAdvise')->get();
            }
        }elseif ($scope==3) {
            $content_id = ExamAdvise::find($id)->exam_id;
            $result = ExamAdvise::adviseAccept($id,$author_id,$author,$status);  
            if($result){
                $bc = ExamAdvise::where([['exam_id',$content_id],['pid',0]])->orderBy('created_at','DESC')->with('allAdvise')->get();
            }
        }
             
        return ['success' => $result? true:false,'advises'=>$bc];
	}
}
