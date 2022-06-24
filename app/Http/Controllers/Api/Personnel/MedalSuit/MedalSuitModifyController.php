<?php

namespace App\Http\Controllers\Api\Personnel\MedalSuit;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personnel\MedalSuit;
use Illuminate\Support\Facades\Auth;

class MedalSuitModifyController extends Controller
{
    //创建新的功章套件
    public function medalSuitCreate(Request $request){
		$title = $request->input('title');
		$type = $request->input('type');
		$amount = $request->input('amount');
		$description = $request->input('description');
		$creator_id = auth('api')->user()->id;
		$s = '';
		$result = MedalSuit::medalSuitAdd($title,$type,$amount,$description,$creator_id);
		if($result) {
			$s = MedalSuit::where('id',$result)->with('getMedals')->first();
		}
    	return [
    		'success'	=> $result? true:false,
    		'medalSuit'	=> $s
    	];
    }
}
