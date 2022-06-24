<?php

namespace App\Http\Controllers\Api\Encyclopedia\EntryResort;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryResort;
use App\Home\Encyclopedia\EntryResort\EntryResortSupportComment;
use App\Home\Encyclopedia\EntryResort\EntryResortEvent;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ResortCommentController extends Controller
{
    //帮助内容的普通评论
    public function support_comment(Request $request){
    	//由于回复的是帮助内容提供，因此此id是对应的帮助id，即asking_id
    	$id = $request->resort_id;
    	$eid = $request->eid;
    	$result = false;
  // return $request;
    		$comment = $request->comment;
    		$author_id = auth('api')->user()->id;
            $author = auth('api')->user()->username;
            $title = $request->title;
        	//将内容写入帮助评论表
        	$result = EntryResortSupportComment::commentAdd($eid,$id,$comment,0,$title,$author_id,$author);
        	//暂时考虑普通回复不添加到事件
        	// $result1 = Encaskingevents::askingEventAdd($id,$author_id,$author,'发布了帮助内容:《'.$title.'》。');
        	//发表了有效的讨论后，积分和成长值+10
            // $result1 = User::expAndGrowValue($author_id,10,10);
        //返回结果
        return ['success' => $result? true:false];
        
    }

    //帮助内容普通评论的回复
    public function comment_reply(Request $request){
    	//此id是对应的普通回复的id，即reply的pid，关系已经转移到了helper_comments表中了
    	$id = $request->comment_id;
    	$resort_id = $request->resort_id;
    	$eid = $request->eid;
    	$result = false;
    	// return $request;
    		$comment = $request->comment;
    		$author_id = auth('api')->user()->id;
            $author = auth('api')->user()->username;
            $title = $request->title;
        	//将内容写入帮助评论表
        	$result = EntryResortSupportComment::commentAdd($eid,$resort_id,$comment,$id,$title,$author_id,$author);
        	//暂时考虑普通回复不添加到事件
        	// $result1 = Encaskingevents::askingEventAdd($id,$author_id,$author,'发布了帮助内容:《'.$title.'》。');
        	//发表了有效的讨论后，积分和成长值+10
            // $result1 = User::expAndGrowValue($author_id,10,10);
        //返回结果
        return ['success' => $result? true:false];
    }
}
