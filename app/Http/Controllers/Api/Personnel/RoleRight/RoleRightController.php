<?php

namespace App\Http\Controllers\Api\Personnel\RoleRight;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Personnel\RoleRight\RoleRight;
use App\Home\Personnel\Role;
use Illuminate\Support\Facades\Auth;

class RoleRightController extends Controller
{
	public function roleRights() {
		$rs = RoleRight::orderBy('sort','asc')->with('role')->get();//不需要分页
		$user_id = Auth::user()->id;
		// 做一个插入的函数起来偷懒
		// RoleRight::truncate();
		// $rights = [
		// 	['message','comment'],
		// 	['surveillance','warning','passWarning'],
		// 	['mark'],
		// 	['synonymMerge','synonymDivide','polysemantMerge','polysemantDivide','punishSlient','punishNoEdit','awardInspector','awardSeniorInspector','informReactFour'],
		// 	['awardFour','dismissPunish','informReactThree'],
		// 	['awardThree','informReactTwo'],
		// 	['awardTwo','informReactOne'],
		// 	['awardOne'],
		// 	['dismissAppoint','move'],
		// 	['research'],
		// 	['awardZero','awardIntendant','awardResearcher'],
		// 	['awardGlobal'],
		// 	['awardSeniorAdministrator']

		// ];
		// for($i=0;$i<count($rights);$i++) {
		// 	if($i==0) {
		// 		$name = 2;	
		// 	} else if ($i==1) {
		// 		$name = 3;
		// 	} else if ($i==2) {
		// 		$name = 4;
		// 	} else if ($i==3) {
		// 		$name = 1;
		// 	} else if ($i==4) {
		// 		$name = 11;
		// 	} else if ($i==5) {
		// 		$name = 5;
		// 	} else if ($i==6) {
		// 		$name = 14;
		// 	} else if ($i==7) {
		// 		$name = 12;
		// 	} else if ($i==8) {
		// 		$name = 6;
		// 	} else if ($i==9) {
		// 		$name = 13;
		// 	} else if ($i==10) {
		// 		$name = 7;
		// 	} else if ($i==11) {
		// 		$name = 8;
		// 	} else if ($i==12) {
		// 		$name = 9;
		// 	}
		// 	for($j=0;$j<count($rights[$i]);$j++) {
		// 		RoleRight::newRoleRight($rights[$i][$j],$name,$i+$j,'introduction',$user_id); 
		// 	}
		// }
		$roles = Role::orderBy('sort','asc')->get();
		return [
            'rights' =>$rs,
            'roles' =>$roles
    	];
	}

    public function roleRightModify(Request $request){
        $data = $request->data;
        $isCreate = $data['isCreate'];
        $result = false;
        $rs = '';
        $user_id = Auth::user()->id;
        if(!$isCreate) {
            $id = $data['id'];
            $right = RoleRight::find($id);
            $right_name = $data['right_name'];
            $role_id = $data['role_id'];
            $sort = $data['sort'];
            $introduction = $data['introduction'];
            if($sort != $right->sort){
                RoleRight::where('sort',$sort)->update(['sort'=>$right->sort]);
            }
            $result = RoleRight::rightUpdate($id,$right_name,$role_id,$sort,$introduction,$user_id); 
        } else {
            $right_name = $data['right_name'];
            $role_id = $data['role_id'];
            $sort = $data['sort'];
            $introduction = $data['introduction'];
            if(RoleRight::where('sort',$sort)->exists()){
                $changes = RoleRight::where('sort','>=',$sort)->get();
                foreach($changes as $value){
                    RoleRight::where('id',$value->id)->update([
                        'sort'=>$value->sort+1
                    ]);
                }
            }
            $result = RoleRight::newRoleRight($right_name,$role_id,$sort,$introduction,$user_id);
        }
        if($result)$rs = RoleRight::orderBy('sort','asc')->with('role')->get();
		return [
    		'success'	=> $result? true:false,
            'rights' =>$rs
    	];
    }
}
