<?php

namespace App\Http\Controllers\Api\Personnel\Behavior;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personnel\Behavior;

class BehaviorModifyController extends Controller
{
    //更改功章属性
    public function behaviorModify(Request $request){
    	$data = $request->data;
        $id = $data['id'];
        $isCreate = $data['isCreate'];
        $name = $data['name'];
        // $sort = $data['sort']; //虽然这里放了sort 但是目前数据表里是没有sort这个字段的
        $score = $data['score'];
        $introduction = $data['introduction'];
    	$behavior = Behavior::find($id);
		$maxSort = count(Behavior::all());
        $bs = '';
        if($isCreate) {
            $result = Behavior::behaviorAdd($name,$score,$introduction);
        } else {
            $result = Behavior::behaviorModify($id,$name,$score,$introduction);
        }
        if($result)$bs = Behavior::get();
		return [
    		'success'	=> $result? true:false,
            'behaviors' =>  $bs
    	];

    }

    //删除id的热度行为，一般肯定不能删除
    public function behaviorDelete(Request $request){
    	$id = $request->input('id');
    	$result = Behavior::behaviorDelete($id);
        if($result)$bs = Behavior::get();
    	return [
    		'success'	=> $result? true:false,
            'behaviors' =>  $bs
    	];
    }
}
