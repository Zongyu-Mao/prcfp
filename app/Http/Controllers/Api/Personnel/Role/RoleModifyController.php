<?php

namespace App\Http\Controllers\Api\Personnel\Role;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personnel\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RoleModifyController extends Controller
{
    //更改角色属性
    public function roleModify(Request $request){
        $data = $request->data;
        $isCreate = $data['isCreate'];
        $result = false;
        $rs = '';
        if(!$isCreate) {
            $id = $data['id'];
            $role = Role::find($id);
            $sort = $data['sort'];
            $rolename = $data['rolename'];
            $creditslower = $data['creditslower'];
            $introduction = $data['introduction'];
            $power_level = $data['power_level'];
            if($sort != $role->sort){
                // 需要更改sort，先得到更改对象，如果有，直接与之交换sort
                Role::where('sort',$sort)->update(['sort'=>$role->sort]);
            }
            $result = Role::roleModify($id,$rolename,$sort,$creditslower,$introduction,$power_level); 
        } else {
            // $maxSort = Role::max('sort');
            // $sort = ($data['sort']>($maxSort+1))?($maxSort+1):$data['sort'];
            // 暂时不去管控$sort
            $sort = $data['sort'];
            $rolename = $data['rolename'];
            $creditslower = $data['creditslower'];
            $introduction = $data['introduction'];
            $creator_id = $data['user_id'];
            $power_level = $data['power_level'];
            if(Role::where('sort',$sort)->exists()){
                $changes = Role::where('sort','>=',$sort)->get();
                foreach($changes as $value){
                    Role::where('id',$value->id)->update([
                        'sort'=>$value->sort+1
                    ]);
                }
            }
            $result = Role::roleAdd($rolename,$sort,$creditslower,$introduction,$creator_id,$power_level);
        }
        if($result)$rs = Role::orderBy('sort','asc')->get();
		return [
    		'success'	=> $result? true:false,
            'roles' =>$rs
    	];
    }

    //删除id的角色
    public function roleDelete(Request $request){
    	$id = $request->input('id');
        $rs = '';
    	$result = Role::roleDelete($id);
        if($result)$rs = Role::orderBy('sort','asc')->get();
    	return [
    		'success'	=> $result? true:false,
            'roles' =>$rs
    	];
    }
}
