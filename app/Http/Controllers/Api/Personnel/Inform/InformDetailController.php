<?php

namespace App\Http\Controllers\Api\Personnel\Inform;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personnel\Inform;
use App\Home\Personnel\MedalSuit;
use App\Home\Personnel\Inform\InformOperateRecord;
use App\Home\Personnel\JudgementInform;
use App\Home\Personnel\JudgementInform\JudgementInformOperateRecord;
use App\Home\Personnel\MessageInform;
use App\Home\Personnel\MessageInform\MessageInformOperateRecord;

class InformDetailController extends Controller
{
    //展示具体举报信息页面
    public function informDetail(Request $request){
    	$scope = $request->scope;
        $id = $request->id;
    	$title = $request->title;
    	$suits = MedalSuit::where('type',2)->with('getMedals')->get();
    	if($scope==1){
    		$inform = Inform::where('id',$id)->with('author')->with('getTarget')->first();
	    	$medalArr = $inform->getMedals;
	    	$records = InformOperateRecord::where('inform_id',$id)->with('getOperator')->get();
    	}elseif($scope==2){
    		$inform = JudgementInform::where('id',$id)->with('author')->with('getTarget')->first();
	    	$medalArr = $inform->getMedals;
	    	$records = JudgementInformOperateRecord::where('inform_id',$id)->with('getOperator')->get();
    	}elseif($scope==3){
    		$inform = MessageInform::where('id',$id)->with('author')->with('getTarget')->first();
	    	$medalArr = $inform->getMedals;
	    	$records = MessageInformOperateRecord::where('inform_id',$id)->with('getOperator')->get();
    	}
    	
    	return $data = array(
    		'suits'		=> $suits,
    		'inform'	=> $inform,
    		'medalArr'	=> $medalArr,
            'records'   => $records,
    		're'	=> $request,
    	);
    }
}
