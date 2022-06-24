<?php

namespace App\Http\Controllers\Api\Encyclopedia\EntryDiscussion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryDiscussion;
use App\Home\Encyclopedia\EntryDiscussion\EntryOpponent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OpponentController extends Controller
{
    //处理反对意见的接受机制
    public function oppose_accept(Request $request){
    	$id = $request->opponent_id;
    	$result = false;
        $opponent = EntryOpponent::find($id);
		$eid = $opponent->eid;
		$author_id = auth('api')->user() -> id;
        $author = auth('api')->user() -> username;
		$recipient = $opponent->author;
		$recipient_id = $opponent->author_id;
		//接受了反对，更改反对意见为已接受，增加接受方
		$result = EntryOpponent::rejectAccept($id,$recipient_id,$recipient,'1');
        return ['success'=>$result? true:false];
	}

	//处理反对意见的拒绝机制
    public function oppose_reject(Request $request){
    	$id = $request->opponent_id;
    	$result = false;
		$eid = $request->eid;
		$opp = EntryOpponent::find($id);
		$deadline = Carbon::now()->addDays(30);
		$title = $request->title;
		$comment = $request->reject;
		$author_id = auth('api')->user()->id;
        $author = auth('api')->user()->username;
		$recipient = $opp->author;
		$recipient_id = $opp->author_id;
		$round = $opp->round + 1;
        if($title && $comment){
            $result = EntryOpponent::rejectAdd($eid,$deadline,$title,$comment,$id,$author_id,$author,$recipient_id,$recipient,$round);
        }
    	return ['success'=>$result? true:false];
    }
}
