<?php

namespace App\Http\Controllers\Api\Cooperation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Home\Cooperation\EntryContributeValue;
use App\Models\Home\Cooperation\ArticleContributeValue;
use App\Models\Home\Cooperation\ExamContributeValue;
use Illuminate\Support\Facades\Auth;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Publication\ArticleCooperation;
use App\Home\Examination\ExamCooperation;

class ContributeValueController extends Controller
{
    //
    public function contributeValueAssign(Request $request) {
        // return $request;
    	$cooperation_id = $request->id;
    	$scope = $request->scope;
    	$data = $request->data;
    	$input_user_id = $request->user_id;
    	$user_id = auth('api')->user()->id;
        $res = 0;
    	$cooperation = '';
    	$result = false;
    	if($scope==1){
    		foreach($data as $user) {
    			$result = EntryContributeValue::contributeUpdate($cooperation_id, $user['id'], $user['value']);
    			$res++;
    		}
            if($res)$cooperation=EntryCooperation::where('id',$cooperation_id)->with('contributions')->first();
    		
    	}elseif($scope==2){
    		foreach($data as $user) {
    			$result = ArticleContributeValue::contributeUpdate($cooperation_id, $user['id'], $user['value']);
    			$res++;
    		}
    		if($res)$cooperation=ArticleCooperation::where('id',$cooperation_id)->with('contributions')->first();
    	}elseif($scope==3){
    		foreach($data as $user) {
    			$result = ExamContributeValue::contributeUpdate($cooperation_id, $user['id'], $user['value']);
    			$res++;
    		}
    		if($res)$cooperation=ExamCooperation::where('id',$cooperation_id)->with('contributions')->first();
    	}
    	return [
	    		'success'=>$result,
                'total'=>$res,
	    		'cooperation'=>$cooperation,
	        ];
    }
}
