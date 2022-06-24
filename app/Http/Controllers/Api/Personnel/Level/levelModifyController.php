<?php

namespace App\Http\Controllers\Api\Personnel\Level;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personnel\Level;

class levelModifyController extends Controller
{
    //更改等级属性
    public function levelModify(Request $request){
        $data = $request->data;
        $isCreate = $data['isCreate'];
        $ls = '';
        if(!$isCreate) {
            // 修改
            $id = $data['id'];
            $level = Level::find($id);
            $sort = $data['sort'];
            $name = $data['name'];
            $creditslower = $data['creditslower'];
            $creditshigher = $data['creditshigher'];
            $introduction = $data['introduction'];
            $editor_id = $data['user_id'];
            if($sort != $level->sort){
                // 需要更改sort，先得到更改对象，如果有，直接与之交换sort
                Level::where('sort',$sort)->update(['sort'=>$level->sort]);
            }
            $result = Level::levelModify($id,$name,$creditslower,$creditshigher,$introduction);
        } else {
            $sort = $data['sort'];
            $name = $data['name'];
            $creditslower = $data['creditslower'];
            $creditshigher = $data['creditshigher'];
            $introduction = $data['introduction'];
            $creator_id = $data['user_id'];
            if(Level::where('sort',$sort)->exists()){
                $changes = Level::where('sort','>=',$sort)->get();
                foreach($changes as $value){
                    Level::where('id',$value->id)->update([
                        'sort'=>$value->sort+1
                    ]);
                }
            }
            $result = Level::levelAdd($sort,$name,$creditslower,$creditshigher,$introduction);
        }
        if($result)$ls = Level::orderBy('sort','asc')->get();
		return [
    		'success'	=> $result? true:false,
            'levels'    =>  $ls
    	];
    }

    //删除id的角色
    public function levelDelete(Request $request){
    	$id = $request->input('id');
    	$result = Level::levelDelete($id);
        $ls = '';
        if($result)$ls = Level::orderBy('sort','asc')->get();
    	return [
    		'success'	=> $result? true:false,
            'levels'    =>  $ls
    	];
    }
}
