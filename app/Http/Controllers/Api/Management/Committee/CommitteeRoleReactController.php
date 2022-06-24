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
use App\Home\Classification;
use Carbon\Carbon;

class CommitteeRoleReactController extends Controller
{
    public function committeeRoleRecord(Request $request) {
    	$data = $request->data;
    	$scope = $data['scope'];
    	$id = $data['id'];
        // return $request;
        // 没有主专业的，不能参与

    	if($scope==1)$record = RoleApplyRecord::where('id',$id)->with('author')->with('role')->with('records')->first();
        if($scope==2)$record = RoleElectRecord::where('id',$id)->with('author')->with('elector')->with('role')->with('records')->first();
        
        $class = Classification::getClassPath($record->author->specialty);
        $mclass = Classification::getClassPath(auth('api')->user()->specialty);
        $user = Auth::user()->with('getCommittee')->first();
        $eclass = (($scope==2&&$record->elector->specialty)?Classification::getClassPath($record->elector->specialty):'');
        return $re = [
        	'record' => $record,
        	'class' => $class,
            'mclass' => $mclass,
        	'user' => $user,
        	'eclass' => $eclass
        ];
    }

    public function committeeRoleReact(Request $request) {
    	$data = $request->data;
        $id = $data['id'];
    	$stand = $data['stand'];
    	$scope = $data['scope'];
        $user_id = $data['user_id'];
        $remark = $data['remark'];
    	$record = ($scope==1?RoleApplyRecord::find($id):RoleElectRecord::find($id));
    	$user = auth('api')->user();
    	$createtime = Carbon::now();
        $result = false;
    	$record = '';
    	if($scope==1 && $user_id==$user->id) {
    		// 申请
    		$result = RoleApplyReactRecord::newRoleApplyReact($user_id,$user->username,$id,$stand,$remark,$createtime);
            if($result)$record = RoleApplyRecord::where('id',$id)->with('author')->with('role')->with('records')->first();
    	} else if($scope==2 && $user_id==$user->id) {
    		// 推举
    		$result = RoleElectReactRecord::newRoleElectReact($user_id,$user->username,$id,$stand,$remark,$createtime);
            if($result)$record = RoleElectRecord::where('id',$id)->with('author')->with('elector')->with('role')->with('records')->first();
    	}
    	
    	return ['success' => $result?true:false,'record'=>$record];
    }
}
