<?php

namespace App\Http\Controllers\Api\Classification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Home\Encyclopedia\Entry;
use App\Home\Publication\Article;
use App\Home\Examination\Exam;
use App\Home\Organization\Group;
use App\Home\Classification;
use Illuminate\Support\Facades\Auth;

class ClassificationBasicContentsController extends Controller
{
    public function basicContents(Request $request){
		$data = $request->data;
		$scope = $data['scope'];
		$pageSize = $data['pageSize'];
		$id = $data['id'];
		// $classname = $data['classname'];
		$cs = ($scope==1?Entry::where('cid',$id)->paginate($pageSize):
	    	($scope==2?Article::where('cid',$id)->paginate($pageSize):
	    		($scope==3?Exam::where('cid',$id)->paginate($pageSize):
	    		    ($scope==4?Group::where('cid',$id)->paginate($pageSize):''))));
		return [
    		'contents'	=> $cs
    	];
    }

    // 需要区别组织
    public function organizations(Request $request){
    	// 此处是假设用户有specialty但是没有设置主组织
		$data = $request->data;
		$user = Auth::user();
		$s = $user->specialty;
		$scope = $data['scope'];
		$type = $data['type'];
		$pageSize = $data['pageSize'];
		$os = '';
		if($scope==4) {
			if($type)$os = Group::where('cid',$s)->paginate($pageSize);
		}
		return [
    		'contents'	=> $os
    	];
    }
}
