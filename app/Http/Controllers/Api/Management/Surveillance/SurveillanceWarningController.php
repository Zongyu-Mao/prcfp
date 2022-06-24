<?php

namespace App\Http\Controllers\Api\Management\Surveillance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Committee\Surveillance\SurveillanceWarning;

class SurveillanceWarningController extends Controller
{
    //
    public function surveillance(Request $request) {
    	$data = $request->data;
    	$scope = $data['scope'];
        $result = 0;
    	$status = 0;
    	// 各种限制后面再来，提交的前提是有问题的需要改进
    	if($scope==1) $basic = Entry::find($data['id'])->only('surveillance','level');
    	if($scope==2) $basic = Article::find($data['id'])->only('surveillance','level');
    	if($scope==3) $basic = Exam::find($data['id'])->only('surveillance','level');
    	if($basic) {
    		// 这时是可以提交巡查结果的
    		$result = SurveillanceWarning::newWarning(auth('api')->user()->id,$data['id'],$data['content'],$status,Carbon::now())
    	}
    	return [
    		'success' => $result?true:false
    	];
    }
}
