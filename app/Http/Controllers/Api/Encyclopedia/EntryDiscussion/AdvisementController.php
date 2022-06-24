<?php

namespace App\Http\Controllers\Api\Encyclopedia\EntryDiscussion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryDiscussion\EntryAdvise;
use App\Home\Encyclopedia\EntryDiscussion\EntryDiscussionEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AdvisementController extends Controller
{
    //处理建议的拒绝机制
    public function advise_reject(Request $request){
    	$id = $request->advise_id;
    	$eid = $request->eid;
    	$result = false;
    	$advise = EntryAdvise::find($id);
		$deadline = Carbon::now()->addDays(30);
		$title = $request->title;
		$comment = $request->reject;
		$author_id = auth('api')->user()->id;
        $author = auth('api')->user()->username;
		$recipient = $advise->author;
		$recipient_id = $advise->author_id;
		$round = $advise->round + 1;
        if($title && $comment){
            $result = EntryAdvise::rejectAdd($eid,$deadline,$title,$comment,$id,$author_id,$author,$recipient_id,$recipient,$round);
        }
	
    	return ['success' => $result? true:false];
    }

    //处理建议的接受机制
    public function advise_accept(Request $request){
    	$id = $request->advise_id;
    	$result = false;
		$author_id = auth('api')->user()->id;
        $author = auth('api')->user()->username;
        $status = '1';
		//接受了反对，更改反对意见为已接受，增加接受方
        $result = EntryAdvise::adviseAccept($id,$author_id,$author,$status);        
        return ['success' => $result? true:false];
	}
}
