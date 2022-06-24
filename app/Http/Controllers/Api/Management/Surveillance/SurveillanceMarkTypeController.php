<?php

namespace App\Http\Controllers\Api\Management\Surveillance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Committee\Surveillance\SurveillanceMarkType;
use Illuminate\Support\Facades\Auth;

class SurveillanceMarkTypeController extends Controller
{
    // 巡视标记的类型,目前不需要分页，不要引入$request
    public function markTypes() {
    	$data = SurveillanceMarkType::all();
    	return $data;
    }
    // 创建或修改巡视标记的类型
    public function markTypeModify(Request $request) {
    	$data = $request->data;
    	$result = false;
    	$user_id = auth('api')->user()->id;
    	$changeType = $data['changeType'];
        $ts='';
    	if($changeType==1) {
    		$result = SurveillanceMarkType::newMarkType($data['title'],$data['sort'],$data['weight'],$data['description'],$user_id,$user_id);
    	}elseif($changeType==2) {
    		$result = SurveillanceMarkType::modifyMark($data['id'],$data['title'],$data['sort'],$data['weight'],$data['description'],$user_id);
    	}
    	if($result) $ts = SurveillanceMarkType::all();
    	return ['success'=>$result?true:false,'types'=>$ts];
    }
}
