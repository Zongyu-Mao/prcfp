<?php

namespace App\Http\Controllers\Api\Encyclopedia\Entry\ChapterOperations;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\Entry\EntryContent;
use App\Home\Encyclopedia\Entry;

class ChapterMoveController extends Controller
{
    //章节移动
    public function entryChapterMove(Request $request){
    	// 该id是content的id
    	$id = $request->content_id;
    	$sort = $request->sort;
    	$eid = $request->entry_id;
    	$isForward = $request->isForward;
    	$result = false;
    	// return $request;
    	if($sort > 1 && $isForward){
    		$sortToChange = EntryContent::where([['eid',$eid],['sort',$sort-1]])->get();
	    	if(count($sortToChange)){
	    		EntryContent::where([['eid',$eid],['sort',$sort-1]])->update(['sort'=>$sort]);
	    	}
    		$result = EntryContent::where('id',$id)->update(['sort' => $sort-1]);
    		// 注意，sortToChange在最后的的话，会产生两个sort=sort-1的东东所以决定先改需要改的的，再改本id的sort	
    	}elseif(!$isForward){
    		$max = max(EntryContent::where('eid',$eid)->pluck('sort')->toArray());
    		if($sort != $max){
    			$sortToChange = EntryContent::where([['eid',$eid],['sort',$sort+1]])->get();
		    	if(count($sortToChange)){
		    		EntryContent::where([['eid',$eid],['sort',$sort+1]])->update(['sort'=>$sort]);
		    	}
	    		$result = EntryContent::where('id',$id)->update(['sort' => $sort+1]);
	    		// 注意，sortToChange在最后的的话，会产生两个sort=sort-1的东东所以决定先改需要改的的，再改本id的sort	
    		}
    	}
    	if($result)$contents=EntryContent::where('eid',$eid)->orderBy('sort','asc')->get();
        return ['success'=>$result ? true:false,'contents'=>$contents];
    }
}
