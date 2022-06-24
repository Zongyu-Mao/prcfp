<?php

namespace App\Http\Controllers\Api\Personal\MyWorks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Organization\Group;
use App\Home\Organization\Group\GroupUser;
use App\Home\Organization\Group\GroupDoc;
use Illuminate\Support\Facades\Auth;

class myGroupController extends Controller
{
    //展示我的组织
    public function myGroups(Request $request){
    	$user = auth('api')->user();
    	$id = $request->id;
        $user_id = $user->id;
    	// 我的自管理组织
    	$manageGroups = Group::where('manage_id',$user_id)->get();

    	// 我的普通组织
    	$involve = GroupUser::where('user_id',$user_id)->pluck('gid')->toArray();
    	$involvedGroups = Group::whereIn('id',$involve)->get();

        // 我的组织文档
        $myGroupDocs = GroupDoc::where('creator_id',$user_id)->get();

        // 我的文档评论

    	return $data = array(
        	'm_groups' => $manageGroups,
        	'n_groups' => $involvedGroups,
        	'docs' => $myGroupDocs,
        );
    }
}
