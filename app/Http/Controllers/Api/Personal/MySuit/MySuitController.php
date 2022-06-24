<?php

namespace App\Http\Controllers\Api\Personal\MySuit;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personnel\MedalSuit;
use App\Home\Personnel\Medal;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MySuitController extends Controller
{
    //我的功章套，这里显示的是我创建的功章套和我参与的公章套
    public function mySuits(){
    	$user = auth('api')->user();
    	$suits = MedalSuit::where('creator_id',$user->id)->with('getMedals')->get();
    	$arr = array_unique(Medal::where('creator_id',$user->id)->pluck('suit_id')->toArray());
    	$arr1 = $suits->pluck('id')->toArray();
    	// 这里我参与的跟我创建的是不共存的
    	$arrc = array_diff($arr, $arr1);
    	$partSuits = MedalSuit::whereIn('id',$arrc)->with('getMedals')->get();
    	return array(
    		'suits' 	=> $suits,
    		'partSuits' => $partSuits
    	);
    }
}
