<?php

namespace App\Http\Controllers\Api\Organization\GroupDoc;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Organization\Group;
use App\Home\Organization\Group\GroupDoc;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GroupDocCreateController extends Controller
{
   	//创建文档
    public function docCreate(Request $request){
    	$gid = $request->id;
    	$result = false;
    	$data = Group::find($gid);
		$title = $request->title;
		$summary = $request->summary;
		$content = $request->content;
		$user = auth('api')->user();
		$result = GroupDoc::docCreate($gid,$title,$summary,$content,$user->id,$user->username);
		return $res = [
    		'success'=>$result ? true:false,
            'id'=>$result
    	];
    }
}
