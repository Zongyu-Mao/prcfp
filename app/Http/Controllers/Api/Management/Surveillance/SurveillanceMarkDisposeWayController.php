<?php

namespace App\Http\Controllers\Api\Management\Surveillance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Committee\Surveillance\SurveillanceMarkDisposeWay;
use Illuminate\Support\Facades\Auth;

class SurveillanceMarkDisposeWayController extends Controller
{
    // 巡视处理
    public function markDisposeWays(Request $request) {
    	$data = SurveillanceMarkDisposeWay::all();
    	return $data;
    }
    // 创建或修改巡视标记的类型
    public function markDisposeWayModify(Request $request) {
    	$data = $request->data;
    	$result = false;
    	$user_id = auth('api')->user()->id;
    	$changeType = $data['changeType'];
        $ds = '';
    	if($changeType==1) {
    		$result = SurveillanceMarkDisposeWay::newMarkDispose($data['title'],$data['sort'],$data['weight'],$data['description'],$user_id,$user_id);
    	}elseif($changeType==2) {
    		$result = SurveillanceMarkDisposeWay::modifyMarkDispose($data['id'],$data['title'],$data['sort'],$data['weight'],$data['description'],$user_id);
    	}
    	if($result) $ds = SurveillanceMarkDisposeWay::all();
    	return ['success'=>$result?true:false,'disposes'=>$ds];
    }
}
