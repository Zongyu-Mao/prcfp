<?php

namespace App\Http\Controllers\Api\Encyclopedia;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\EntryResort;
use App\Home\Encyclopedia\EntryResort\EntryResortSupportComment;
use App\Home\Encyclopedia\EntryResort\EntryResortEvent;
use App\Notifications\Encasking\EncaskingNotification;
use App\Home\Encyclopedia\Entry;

class EntryResortController extends Controller
{
    //展示页
	public function entryResort(Request $request){
		$id = $request->entry_id;
		$title = $request->entry_title;
		$entry = Entry::find($id)->only('id','title','cid','manage_id');
		if($id && $entry){
			$asking_count = EntryResort::where([['eid',$id],['pid','0']])->exists() ? '1':'0';
			$helper_count = EntryResort::where([['eid',$id],['pid','!=','0']])->exists() ? '1':'0';
			//判断评审中是否存在协作计划，如果存在协作计划，接收反对的选项应对协作小组可见，否则，对自管理员可见
	    	$cooperationCount = EntryCooperation::where([['eid',$id],['status','0']])->exists() ? '1':'0';
            $cooperation = EntryCooperation::where([['eid',$id],['status','0']])->first();
	    	$manage_id = $entry['manage_id'];
            $array_encoo_crew_ids = [];
            if($cooperation)$array_encoo_crew_ids =$cooperation->crews()->pluck('user_id')->toArray();
	    	//如果存在求助话题
    		if($asking_count){
    			$data_asking = EntryResort::where([['eid',$id],['pid','0'],['status','0']])->with('helpers')->orderBy('created_at','DESC')->get();
    		}else{
    			$data_asking = '';
    		}
            // dd($data_asking);

    		if($helper_count){
    			$data_helper = EntryResort::where([['eid',$id],['pid','!=','0']])->orderBy('created_at','DESC')->get();
    		}else{
    			$data_helper = '';
    		}

    		$helper_comment_count = EntryResortSupportComment::where([['eid',$id],['type','0'],['pid','0']])->exists() ? '1':'0';
    		if($helper_comment_count){
    			$helper_comment = EntryResortSupportComment::where([['eid',$id],['type','0'],['pid','0']])->with('allComment')->orderBy('created_at','DESC')->get();
    		}else{
    			$helper_comment = '';
    		}
    		
    		$help_reject_count = EntryResortSupportComment::where([['eid',$id],['type','1'],['pid',0]])->exists() ? '1':'0';
    		if($help_reject_count){
    			$help_reject = EntryResortSupportComment::where([['eid',$id],['type','1'],['pid',0]])->orderBy('created_at','DESC')->get();
    		}else{
    			$help_reject = '';
    		}
            $events = EntryResortEvent::where('eid',$id)->orderBy('created_at','desc')->limit(15)->get();
    		// dd($helper_child_comment);
    		// dd($data_helper);
    		// $helpers = Encasking::find(6)->enchelpers()->get();
    		// $helper_count = Enchelper::where('enc_id',$id)->exists() ? '1':'0';
    		// if($helper_count){
    		// 	$data_helper = Enchelper::where('enc_id',$id)->orderBy('created_at','DESC')->get();
    		// }else{
    		// 	$data_helper = '';
    		// }
    		// dd($helpers);
			// dd($data_asking);
			$data = array(
	    		'basic'		=> $entry,
	    		'manage_id'		=> $manage_id,
	    		'help_reject_count'		=> $help_reject_count,
	    		'crews'		=> $array_encoo_crew_ids,
	    		'resorts' => $data_asking,
	    		'helpers' => $data_helper,
	    		'helper_comments' => $helper_comment,
                'help_rejects' => $help_reject,
	    		'events' => $events,

	    	);


		return $data;
		// return view('home/encyclopedia/entryResort',compact('data','data_asking','data_helper','helper_comment','help_reject'));
		}
	}
}
