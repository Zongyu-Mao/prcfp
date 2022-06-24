<?php

namespace App\Http\Controllers\Api\Encyclopedia;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryDiscussion;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\EntryDiscussion\EntryOpponent;
use App\Home\Encyclopedia\EntryDiscussion\EntryAdvise;
use App\Home\Encyclopedia\EntryDiscussion\EntryDiscussionEvent;
use App\Home\Encyclopedia\Entry;
use App\Models\User;
use Carbon\Carbon;

class EntryDiscussionController extends Controller
{
    //首页展示
    public function entryDiscussion(Request $request){
    	$id = $request->entry_id;
        $title = $request->entry_title;
    	$entry = Entry::find($id);
    	$data = '';
    	//如果请求的id存在且查询符合
    	if($id && $entry->title==$title){
	    	// $enc_level = Encyclopedia::where('id',$id)->first()->level;
	    	//查看该词条下是否有讨论内容
	    	$data_oppose = '';
	    	$data_advise = '';
	    	$data_discuss = '';
	    	$data_event = '';
	    	$array_encoo_crew_ids = [];
	    	$cooperation = EntryCooperation::find($entry->cooperation_id);
	    	//判断评审中是否存在协作计划，如果存在协作计划，接收反对的选项应对协作小组可见，否则，对自管理员可见           
	    	if($cooperation && $cooperation->status==0){
	    		//如果存在活跃的协作计划，取得协作计划成员组
	    		$cooperation = EntryCooperation::find($entry->cooperation_id);
	    		$initiate_id = $entry->manage_id;
                $array_encoo_crew_ids = $cooperation->crews()->pluck('user_id')->toArray();

	    		if($initiate_id)array_push($array_encoo_crew_ids, $initiate_id);
	    	}else{
	    		//如果没有活跃的协作计划，评审由自管理员托管
	    		$initiate_id = $entry->manage_id;
	    		if($initiate_id)array_push($array_encoo_crew_ids, $initiate_id);
	    	}
	    	// 这段考虑一下重新替换，现在不做更改

			//取得反对内容
			if(EntryOpponent::where('eid',$id)->exists()){
				$data_oppose = EntryOpponent::where([['eid',$id],['pid',0]])->with('allOppose')->orderBy('created_at','DESC')->get();
			}
			//取得建议内容
			if(EntryAdvise::where('eid',$id)->exists()){
				$data_advise = EntryAdvise::where([['eid',$id],['pid',0]])->orderBy('created_at','DESC')->with('allAdvise')->get();
			}
			//取得普通讨论内容
			if(EntryDiscussion::where('eid',$id)->exists()){
				$data_discuss = EntryDiscussion::where([['eid',$id],['pid',0]])->orderBy('created_at','DESC')->with('allDiscuss')->get();
			}
			//取得词条讨论的事件内容
			$discuss_event_count = EntryDiscussionEvent::where('eid',$id)->exists() ? '1':'0';;
			if($discuss_event_count){
				$data_event = EntryDiscussionEvent::where('eid',$id)->orderBy('created_at','DESC')->limit(20)->get();
			}
	    	
	    	$data = array(
	    		'basic'		=> $entry,
	    		'crews'		=> $array_encoo_crew_ids,
	    		'opposes'	=> $data_oppose,
	    		'advises'	=> $data_advise,
	    		'discussions'	=> $data_discuss,
	    		'events'		=> $data_event
	    	);
	    	
    	}
    	return $data;
    } 
}
