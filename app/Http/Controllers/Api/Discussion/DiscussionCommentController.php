<?php

namespace App\Http\Controllers\Api\Discussion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryDiscussion;
use App\Home\Publication\ArticleDiscussion;
use App\Home\Examination\ExamDiscussion;
use Illuminate\Support\Facades\Auth;

class DiscussionCommentController extends Controller
{
    //处理讨论区回复
 	public function discussion_reply(Request $request){
 		$id = $request->pid;
 		// 这里需要的是content_id,但是传过来的是discussion的id 
 		$content_id = $request->id;
 		$scope = $request->scope;
 		$result = false;
		$title = $request->title;
		$comment = $request->reply;
		$author_id = auth('api')->user()->id;
        $author = auth('api')->user()->username;
        $bc = '';
        if($comment){
            //写入评论表，pid=0顶级评论，type=0支持（回复默认全部为支持）
            if($scope==1){
            	$result = EntryDiscussion::discussionAdd($content_id,$title,$comment,$id,$author_id,$author);
                if($result) {
                    $bc = EntryDiscussion::where([['eid',$content_id],['pid',0]])->orderBy('created_at','DESC')->with('allDiscuss')->get();
                }
            }elseif($scope==2){
            	$result = ArticleDiscussion::discussionAdd($content_id,$title,$comment,$id,$author_id,$author);
                if($result) {
                    $bc = ArticleDiscussion::where([['aid',$content_id],['pid',0]])->orderBy('created_at','DESC')->with('allDiscuss')->get();
                }
            }elseif($scope==3){
                $result = ExamDiscussion::discussionAdd($content_id,$title,$comment,$id,$author_id,$author);
                if($result) {
                    $bc = ExamDiscussion::where([['exam_id',$content_id],['pid',0]])->orderBy('created_at','DESC')->with('allDiscuss')->get();
                }
            } 
		}
		return ['success'=>$result? true:false,'discussions'=>$bc];
 	}
}
