<?php

namespace App\Http\Controllers\Api\Organization\GroupDoc;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Organization\Group\GroupDoc;
use App\Home\Organization\Group\GroupDoc\GroupDocComment;
use Illuminate\Support\Facades\Auth;

class GroupDocCommentController extends Controller
{
    //添加首级评论
    public function commentAdd(Request $request){
    	$id = $request->id;
    	$did = $request->did;
    	$gid = $request->gid;
    	$title = $request->title;
    	$comment = $request->comment;
    	$reply = $request->reply;
    	$result = false;
        $comments = '';
    	// this id is for doc
    	$doc = GroupDoc::find($did);
    	if($doc->status == 0){
	        $user = auth('api')->user();
	        if(!$reply){
	        	$result = GroupDocComment::commentAdd($did,$title,$comment,0,$user->id,$user->username);
	        }else{
	        	$result = GroupDocComment::commentReply($did,$title,$comment,$id,$user->id,$user->username);
	        }  		
    	}
        $comments = GroupDocComment::where([['did',$did],['pid',0]])->with('allComment')->orderBy('created_at','desc')->get();
    	return $res = [
    		'success'=>$result ? true:false,
            'comments'=>$comments
    	];
    }
}
