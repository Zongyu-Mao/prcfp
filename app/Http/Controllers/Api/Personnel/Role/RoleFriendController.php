<?php

namespace App\Http\Controllers\Api\Personnel\Role;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Personnel\Role\RoleApplyRecord;
use App\Models\Personnel\Role\RoleApplyReactRecord;
use App\Models\Personnel\Role\RoleElectRecord;
use App\Models\Personnel\Role\RoleElectReactRecord;
use Illuminate\Support\Facades\Auth;

class RoleFriendController extends Controller
{
    //返回数组角色申请信息
    public function getRoleRecords(Request $request) {
    	$data = $request->data;
    	$fs = $data['friends'];
    	// return $data['friends'];
    	$as = RoleApplyRecord::whereIn('user_id',$fs)->where('status',0)->pluck('user_id')->toArray();
    	$es = RoleElectRecord::whereIn('elect_id',$fs)->where('status',0)->pluck('user_id')->toArray();
    	return  [
			'applies'=>array_unique($as),
			'elects'=>array_unique($es)
		];
    }
}
