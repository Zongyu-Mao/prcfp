<?php

namespace App\Http\Controllers\Api\Encyclopedia;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\Entry\EntryContent;
use App\Home\Encyclopedia\EntryDynamic;
use App\Models\User;

class EntryHistoryController extends Controller
{
    //历史页的显示
    public function entryHistory(Request $request,$id,$title){
    	$entry = Entry::find($id);
    	$dynamics = EntryDynamic::where('eid',$id)->orderBy('createtime','DESC')->limit(20)->get();
    	return $dynamics;
    	
    }
}
