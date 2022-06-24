<?php

namespace App\Http\Controllers\Api\Classification;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Classification;
use Illuminate\Support\Facades\Auth;

class ClassificationAdditionController extends Controller
{
    //选择增加分类页面
    public function addition(Request $request){
		$classname = $request->classname;
		$pid = $request->pid;
		$level = $request->level;
		$result = false;
		// 这里不打算判断是否重复了~~
		$result = Classification::addClass($classname,$pid,$level,auth('api')->user()->id,auth('api')->user()->username);
		return [
    		'success'	=> $result? true:false,
    		'new_class'	=> $result
    	];
    }

    //修改分类
	public function modify(Request $request){
		// return $request;
		$id_modify = $request->id;
		$classname = $request->classname;
		$result = false;
		$result = Classification::modifyClass($classname,$id_modify,auth('api')->user()->id,auth('api')->user()->username);
		return [
    		'success'	=> $result? true:false
    	];
    }

    // ajax追加分类内容
    public function getClassChildrenById(Request $request){

		$pid = $request->id;
		$class = Classification::where('pid',$pid)->get();
		//向前端返回json数据
		return $class;
    	
    }

    // 获取已有的层级分类
    public function getReadyMadeClass(Request $request){
    	$level = $request->level;
    	$id = $request->id;
    	// 如果level==1，只需要给到第一级分类，如果level>1，这时要传送较多内容
    	$c_a = Classification::where('pid',0)->get();
		$class = Classification::where('level',$level)->get();
		//向前端返回json数据
		return array(
			'classificationA' => $c_a
		);
    	
    }

}
