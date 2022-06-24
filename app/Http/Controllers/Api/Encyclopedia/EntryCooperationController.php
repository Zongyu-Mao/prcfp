<?php

namespace App\Http\Controllers\Api\Encyclopedia;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\Entry;
use App\Home\Classification;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationDiscussion;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationVote;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationEvent;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationMessage;
use App\Models\Encyclopedia\Ambiguity\Synonym;
use App\Home\UserDynamic;
use App\Models\User;
use DB;
use Input;
use Carbon\Carbon;

class EntryCooperationController extends Controller
{
    //
    public function getEntryCooperation(Request $request,$id,$title) {
    	$entry = Entry::find($id);
    	// if($entry->status==5){
    	// 	$entry = Entry::find(Synonym::where('sid',$id)->first()->eid);
    	// }
        // dd($cooperation);
        if($entry && $entry->status!=5){
        	$cooperation = EntryCooperation::where('id',$entry->cooperation_id)->with('crews')->with('contributions')->first();
	        if($cooperation){
	        	$cooperationId = $cooperation->id;

		    	$data_class = Classification::getClassPath($cooperation->cid);

		        //取出协作讨论信息
		        $discussion = EntryCooperationDiscussion::where('cooperation_id',$cooperationId)->orderBy('created_at','desc')->limit(15)->get();

		        // dd($count);
		        //读取协作投票信息
	            $data_votes = EntryCooperationVote::where([['cooperation_id',$cooperationId],['status','0']])->orderBy('created_at','desc')->with('getVoteRecord')->get();
	            $history_votes=EntryCooperationVote::where([['cooperation_id',$cooperationId],['status','>','0']])->orderBy('created_at','desc')->with('getVoteRecord')->get();

	            $crewArr = $cooperation->crews()->pluck('user_id')->toArray();
	            array_push($crewArr,$entry->manage_id);
	            // dd($data_votes);
	            $userDynamics = UserDynamic::whereIn('user_id',$crewArr)->orderBy('createtime','desc')->limit('20')->get();
	            // dd($data_votes);

		        //读取协作计划的事件和动态

		        $data_events = EntryCooperationEvent::where('cooperation_id',$cooperationId)->orderBy('created_at','desc')->limit(15)->get();

		        //读取协作计划面板的用户留言
	            $data_message = EntryCooperationMessage::where([['cooperation_id',$cooperationId],['pid','0']])->orderBy('created_at','desc')->with('reply')->get();
	            $crews = User::whereIn('id',$crewArr)->with('getAvatar')->get();
		        // dd($data_event);
		        $return = array(
		            'basic'         => $entry,
		            'cooperation'   => $cooperation,
		            'crews'   		=> $crews,
		            'crewArr'   	=> $crewArr,
		            'data_class'    => $data_class,
		            'discussion' 	=> $discussion,
		            'votes'   		=> $data_votes,
		            'history_votes' => $history_votes,
		            'data_events' 	=> $data_events,
		            'userDynamics' 	=> $userDynamics,
		            'data_message' 	=> $data_message
		        );
	        }else {
	        	$return = array(
		            'basic'         => $entry,
		            'cooperation'   => $cooperation
		        );
	        }
	    return $return;
	    }
    }
}
