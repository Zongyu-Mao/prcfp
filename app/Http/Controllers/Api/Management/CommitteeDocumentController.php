<?php

namespace App\Http\Controllers\Api\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Committee\CommitteeDocument;
use App\Models\Committee\CommitteeDocumentComment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CommitteeDocumentController extends Controller
{
    //
    public function committeeDocument(Request $request) {
    	$data = $request->data;
    	$title = $data['title'];
    	$id = $data['id'];
    	$document = CommitteeDocument::where([['id',$id],['title',$title]])->first();
    	$comments = CommitteeDocumentComment::where([['did',$id],['pid',0]])->with('allComment')->get();
    	return ['document'=>$document,'comments'=>$comments];

    }
    public function committeeDocumentCreate(Request $request) {
    	$data = $request->data;
    	$title = $data['title'];
    	$content = $data['content'];
    	$tcid = $data['tcid'];
    	$result = 0;
    	$user = auth('api')->user();
    	$status = 1;
        $ds = '';
    	$result = CommitteeDocument::newCommitteeDocument($tcid,$title,$content,$status,$user->id);
        if($result)$ds = CommitteeDocument::where('tcid',$tcid)->orderBy('created_at','desc')->get();
    	return ['success'=>$result?true:false,'docs'=>$ds];

    }

    public function committeeDocumentCommentCreate(Request $request) {
    	$data = $request->data;
    	$title = $data['title'];
    	$comment = $data['comment'];
    	$isCreate = $data['isCreate'];
    	$did = $data['pid'];
    	$result = 0;
        $cs = '';
    	$user = auth('api')->user();
    	$pid = $isCreate?0:$data['parentCommentId'];
    	$createtime=Carbon::now();
    	$result = CommitteeDocumentComment::commentAdd($did,$title,$comment,$pid,$user->id,$user->username,$createtime);
        if($result)$cs = CommitteeDocumentComment::where([['did',$did],['pid',0]])->with('allComment')->get();
    	return ['success'=>$result?true:false,'comments'=>$cs];

    }

    public function committeeDocumentModify(Request $request) {
    	$data = $request->data;
    	$content = $data['content'];
    	$id = $data['id'];
    	$tcid = $data['tcid'];
    	$result = 0;
    	$user = auth('api')->user();
    	if($user->id ==CommitteeDocument::find($id)->creator_id){
    		$result = CommitteeDocument::modifyCommitteeDocument($id,$tcid,$content);
    	}
    	
    	return ['success'=>$result?true:false];

    }
}
