<?php

namespace App\Http\Controllers\Api\Management\Committee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Home\Personnel\Role;
use App\Models\Personnel\Role\RoleApplyRecord;
use App\Models\Personnel\Role\RoleApplyReactRecord;
use App\Models\Personnel\Role\RoleElectRecord;
use App\Models\Personnel\Role\RoleElectReactRecord;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CommitteeRoleController extends Controller
{
    //本控制器控制委员会管理组页面角色权限的申请与推举
    public function committeeRoles(Request $request) {
    	// 权限信息
        $data = $request->data;
        $scope = $data['scope'];
        $pageSize = $data['pageSize'];
        if($scope==1)$roleMsgs = RoleApplyRecord::with('author')->with('role')->with('records')->orderBy('createtime','desc')->paginate($pageSize);
        if($scope==2)$roleMsgs = RoleElectRecord::with('author')->with('elector')->with('role')->with('records')->orderBy('createtime','desc')->paginate($pageSize);
        return $roleMsgs = [
            
        	'roleMsgs' => $roleMsgs
        ];
    }
    public function roleReactRecord(Request $request) {
    	$data = $request->data;
    	$type = $data['type'];
    	$id = $data['id'];
    	$user_id = $data['user_id'];
    	$result = 0;
    	if($type==1) {
    		// 申请记录处理
    		$result = RoleApplyReactRecord::where([['user_id',$user_id],['apply_id',$id]])->first();
    	} else if($type==2) {
    		// 推举记录处理
    		$result = RoleElectReactRecord::where([['user_id',$user_id],['elect_id',$id]])->first();
    	}
    	return ['check' => $result?true:false,'record' => $result];
    }
    
}
