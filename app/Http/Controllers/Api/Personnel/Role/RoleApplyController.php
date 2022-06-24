<?php

namespace App\Http\Controllers\Api\Personnel\Role;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Personnel\Role\RoleApplyRecord;
use App\Models\Personnel\Role\RoleElectRecord;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RoleApplyController extends Controller
{
    //申请角色
    public function roleApply(Request $request) {
    	$data = $request->data;
    	$id = $data['id'];
    	$user = auth('api')->user();
    	$user_id = $data['user_id'];
    	$result = 0;
    	// 一堆审核
    	$status = 0;
    	$remark = $data['remark'];
    	$createtime = Carbon::now();
    	$result = RoleApplyRecord::newRoleApply($user_id,$id,$status,$remark,$createtime);
    	return  [
			'success'=>$result?true:false,
			'result'=>$result
		];
    }
    //推举角色
    public function roleElect(Request $request) {
    	$data = $request->data;
    	$id = $data['id'];
    	$user = auth('api')->user();
    	$user_id = $data['user_id'];
    	$elect_id = $data['elect_id'];
    	$result = 0;
    	// 一堆审核
    	$status = 0;
    	$remark = $data['remark'];
    	$createtime = Carbon::now();
    	$result = RoleElectRecord::newRoleElect($user_id,$elect_id,$id,$status,$remark,$createtime);
    	return  [
			'success'=>$result?true:false,
			'result'=>$result
		];
    }
}
