<?php

namespace App\Http\Controllers\Api\Organization\Group;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Home\Organization\Group;

class PrimaryGroupController extends Controller
{
    //
    public function primaryGroup(Request $request){
    	$data = $request->data;
    	$id = $data['id'];
    	$user = Auth::user();
    	$group = Group::find($id);
    	$gid = $user->gid;
    	$cid = $user->getCommittee->cid;
    	$result = '';
    	if($id!=$gid && $user->specialty==$group->cid) {
    		if( $gid==0 || ($id>0&&$user->update(['gold'=>$user->gold-1])) ) {
    			$result = $user->update(['gid'=>$id]);
    		}
    	}

    	return [
			'success'=>$result?true:false,
		];
	}
}
