<?php

namespace App\Http\Controllers\Api\Personal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personal\Relationship\UserFocusRelationship;
use App\Home\Personal\Relationship\UserFriendApplicationRecord;
use App\Home\Personal\Relationship\UserFriendRelationship;
use App\Home\Personal\Relationship\FriendPrivateLetter;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PersonalRelationshipController extends Controller
{
    //设置用户的关注
    public function userFocus(Request $request){
    	$user_id = auth('api')->user()->id;
        $focus_id = $request->id;
		$stand = $request->stand;
		$result = false;
		if($stand==1 && $user_id != $focus_id && !UserFocusRelationship::where([['user_id',$user_id],['focus_id',$focus_id]])->exists()){
			$createtime = Carbon::now();
			$result = UserFocusRelationship::focusRelationshipAdd($user_id,$focus_id,$createtime);
		}elseif($stand==2 && $user_id != $focus_id && UserFocusRelationship::where([['user_id',$user_id],['focus_id',$focus_id]])->exists()){
            $result = UserFocusRelationship::where([['user_id',$user_id],['focus_id',$focus_id]])->delete();
        }
    	return [
    		'success'	=> $result? true:false
    	];
    }

    // 添加好友申请
    public function friendApplication(Request $request){
    	$id = $request->id;
    	$user = User::find($id);
    	$user_id = $user->id;
    	$result = false;
    	$application_id = auth('api')->user()->id;
    	if($user && $application_id != $user_id){
    		$username = $user->username;
    		$application_username = auth('api')->user()->username;
    		$title = $request->get('title');
    		$content = $request->get('letter');
    		$applyResult = '0';
    		// 两重判断，如果双方有任何一方有过添加记录，无法继续添加好友行为emmmm，想了想，还是先单方判定，毕竟被申请方全力剥夺貌似有点过分，再考虑考虑
            //  || !UserFriendApplicationRecord::where([['user_id',$application_id],['application_id',$user_id]]->exists()
    		if(!UserFriendApplicationRecord::where([['user_id',$user_id],['application_id',$application_id],['applyResult',0]])->exists()){
    			$createtime = Carbon::now();
    			$result = UserFriendApplicationRecord::friendApplicationRecord($user_id,$username,$application_id,$application_username,$title,$content,$applyResult);
	    		}
    	}
    	return [
    		'success'	=> $result? true:false
    	];
    }

    // 同意添加好友申请
    public function friendApplicationStand(Request $request){
    	$result = false;
        $stand = $request->stand;
        $id = $request->id;
        // stand为1是同意2是拒绝
		$result = UserFriendApplicationRecord::friendApplicationStand($id,$stand);
    	return [
    		'success'	=> $result? true:false
    	];
    }

    public function userMessageCheck(Request $request) {
        $check_id = $request->user_id;
        $user_id = auth('api')->user()->id;
        $check='';
        if($check_id!=$user_id)$check = User::find($check_id);
        return ['check'=>$check];
    }

}
