<?php

namespace App\Http\Controllers\Api\Encyclopedia;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryDebate;
use App\Home\Encyclopedia\EntryReview;
use App\Home\Encyclopedia\EntryDebate\EntryDebateEvent;
use App\Home\Encyclopedia\EntryReview\EntryReviewEvent;
use App\Home\Encyclopedia\EntryDebate\EntryDebateComment;
use App\Home\Encyclopedia\EntryDiscussion\EntryOpponent;
use App\Home\Encyclopedia\EntryDiscussion\EntryDiscussionEvent;
use App\Home\Encyclopedia\EntryReview\EntryReviewOpponent;
use App\Home\Encyclopedia\Entry;

class EntryDebateController extends Controller
{
    //首页显示
    public function entryDebate(Request $request,$id,$title){
    	$entry = Entry::find($id);
    	$type = $request->type;
    	$typeID = $request->type_id;
    	$data_debateAll = '';
    	$data_comments = '';
    	$starRecord = [];
    	$debateFrom = '';
    	if($id && $title==$entry->title){
    		// 取得debate数据,否则为空
    		if(EntryDebate::where('eid',$id)->exists()){
    			$data_debateAll = EntryDebate::where('eid',$id)->orderBy('created_at','DESC')->get();
    		}
    	}
    	return $data = array(
    		'debate_all'	=> $data_debateAll,
    		);
    	
    }

    //单debate的详情
    public function debate(Request $request){
    	$id = $request->eid;
    	$type = $request->type;
    	$typeID = $request->type_id;
    	$data_comments = '';
    	$starRecords = [];
    	$debateFrom = '';
    	// return $request;
    	// return EntryDebate::where('eid',$id)->get();
    	// return EntryDebate::where([['eid',$id],['type',$type],['type_id',$typeID]])->first();;
    	if($type && $typeID){
			// 这里得到具体的debate了
			$debate = EntryDebate::where([['eid',$id],['type',$type],['type_id',$typeID]])->with('getStars')->first();
            if($debate){
                $debate_id = $debate->id;
                // $starRecord = $debate->getStars();
                // $starRecord = $debate->getStars->pluck('user_id')->toArray();
                // array_push($starRecord,$debate->Aauthor_id);
                // array_push($starRecord,$debate->Bauthor_id);
                // if($debate->referee_id){
                // 	array_push($starRecord,$debate->referee_id);
                // };
                // $starRecords = array_unique($starRecord);
                //判断网友留言是否存在
                if(EntryDebateComment::where([['eid',$id],['debate_id',$debate_id],['pid',0]])->exists()){
					$data_comments = EntryDebateComment::where([['eid',$id],['debate_id',$debate_id],['pid',0]])->with('allComment')->orderBy('created_at','DESC')->get();
				}
    			
    		}
			// dd($debate);
			if($type == 1){
				$debateFrom = EntryReviewOpponent::find($typeID)->title;
			}elseif($type == 2){
				$debateFrom = EntryOpponent::find($typeID)->title;
			}
            $events = EntryDebateEvent::where('debate_id',$debate->id)->orderBy('created_at','desc')->limit(20)->get();
	    	
    	}
    	return $data = array(
    		'debate'		=> $debate,
    		'comments'		=> $data_comments,
            'debateFrom'    => $debateFrom,
    		'events'	=> $events
    		);
    	
    }
}
