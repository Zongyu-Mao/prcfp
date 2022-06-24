<?php

namespace App\Http\Controllers\Api\Personal\MyProp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personnel\Prop;
use App\Home\Personnel\Prop\UserProp;
use App\Home\Personnel\Medal;
use App\Home\Personnel\MedalSuit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MyPropController extends Controller
{
    //展示我的道具
    public function myProps(Request $request){
    	$user = Auth::user();
    	// $props = $user->getProps;
    	$data = $request->data;
        $user_id = $user->id;
        $pageSize = $data['pageSize'];
        $attr = $data['attr_scope'];
        $contents = '';
    	if($attr=='suit') {
    		// $suits = MedalSuit::where('creator_id',$user->id)->with('getMedals')->get();
	    	// $arr = array_unique(Medal::where('creator_id',$user->id)->pluck('suit_id')->toArray());
	    	// $arr1 = $suits->pluck('id')->toArray();
	    	// // 这里我参与的跟我创建的是不共存的
	    	// $arrc = array_diff($arr, $arr1);
	    	$contents = MedalSuit::where('creator_id',$user->id)->with('getMedals')->paginate($pageSize);
    	} else if($attr=='medal') {
    		$contents = Medal::where('creator_id',$user_id)->with('getSuit')->orderBy('suit_id','desc')->orderBy('sort','desc')->paginate($pageSize);
    	}

    	return array(
    		'contents' 	=> $contents
    	);
    }
}
