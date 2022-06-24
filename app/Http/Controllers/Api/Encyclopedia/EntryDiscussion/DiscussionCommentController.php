<?php

namespace App\Http\Controllers\Api\Encyclopedia\EntryDiscussion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryDiscussion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DiscussionCommentController extends Controller
{
    //处理讨论区回复
 	public function discussion_reply(Request $request){
 		$id = $request->pid;
 		$eid = $request->eid;
 		$result = false;
		$title = $request->title;
		$comment = $request->reply;
		$author_id = auth('api')->user()->id;
        $author = auth('api')->user()->username;
        if($comment){
            //写入评论表，pid=0顶级评论，type=0支持（回复默认全部为支持）
            $result = EntryDiscussion::discussionAdd($eid,$title,$comment,$id,$author_id,$author);
		}
		return ['success'=>$result? true:false];
 	}
}
