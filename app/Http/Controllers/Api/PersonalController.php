<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personal\UserPicture;
use App\Home\Personal\Relationship\UserFocusRelationship;
use App\Home\Personal\Relationship\UserFriendRelationship;
use App\Home\Encyclopedia\Entry;
use App\Home\Publication\Article;
use App\Home\Examination\Exam;
use App\Home\Organization\Group;
use App\Home\Encyclopedia\EntryDynamic;
use App\Home\Publication\Article\ArticleDynamic;
use App\Home\Examination\Exam\ExamDynamic;
use App\Home\Organization\Group\GroupDynamic;
use App\Home\Personal\UserClass;
use App\Home\Classification;
use App\Home\UserDynamic;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Personal\PrivateMedal;
use App\Home\Personnel\Inform\PunishRecord;
use App\Home\Personal\Relationship\UserFriendApplicationRecord;

class PersonalController extends Controller
{
    //展示我的主页
    public function personalHomepage(Request $request){
    	$id = $request->id;
        $user = auth('api')->user();
    	$self = $user->id;
        $p_medals = PrivateMedal::where('owner_id',($id?$id:$self))->orderBy('updated_at','desc')->get();
        $pu_medals = PunishRecord::where('punish_id',($id?$id:$self))->with('medal')->orderBy('createtime','desc')->get();
    	if($self == $id || $id==0){
    		$id = $self;
    		$data = User::where('id',$id)->with('getAvatar')->with('getRole')->with('getLevel')->first();
            // 获取好友数据（好友是双向的）
            $friendProsArr = UserFriendRelationship::where('user_id',$id)->pluck('friend_id')->toArray();
            $friendConsArr = UserFriendRelationship::where('friend_id',$id)->pluck('user_id')->toArray();
            $friendsArr = array_unique(array_merge($friendProsArr,$friendConsArr));
            $friends = User::whereIn('id',$friendsArr)->with('getAvatar')->get();
            // 关注用户数据
            $focusUsers = UserFocusRelationship::where('user_id',$id)->pluck('focus_id')->toArray();
            array_push($friendsArr,$id);
            array_unique(array_merge($friendsArr,$focusUsers));
            // 用户动态包括自己动态、好友动态和关注动态
            // dd($friendsArr);
            $dynamics = UserDynamic::whereIn('user_id',$friendsArr)->limit(15)->orderBy('createtime','desc')->get();
            
            $focusArr = User::find($id)->getFocusUsers->pluck('id')->toArray();

            $return = array(
	    		'user'		=> $data,
	    		'dynamics'		=> $dynamics,
	    		'focusArr'	=> $focusArr,
	    		'friends'	=> $friends,
                'friendsArr'    => $friendsArr,
                'p_medals'    => $p_medals,
	    		'pu_medals'	=> $pu_medals,
	    	);

    	}elseif($self!=$id && User::where('id',$id)->exists()){
    		// 如果用户存在但不是自己，这时候只需要展示用户的动态和user信息就可以了
    		$data = User::where('id',$id)->with('getAvatar')->with('getRole')->with('getLevel')->first();
            // 获取好友数据（好友是双向的）
            $isFriend = UserFriendRelationship::where([['user_id',$self],['friend_id',$id]])->orWhere([['user_id',$id],['friend_id',$self]])->exists();
            $isApplicated = UserFriendApplicationRecord::where([['user_id',$id],['application_id',$user->id],['applyResult',0]])->exists();
            $isFocus = UserFocusRelationship::where([['user_id',$self],['focus_id',$id]])->exists();

            $dynamics = UserDynamic::where('user_id',$id)->limit(15)->orderBy('createtime','desc')->get();

            // 添加是否为正在申请的好友状态***********************
    		$return = array(
	    		'user'		=> $data,
                'isFriend'      => $isFriend,
	    		'isApplicated'		=> $isApplicated,
	    		'isFocus'	=> $isFocus,
	    		'dynamics'	=> $dynamics,
                'p_medals'    => $p_medals,
                'pu_medals' => $pu_medals,
	    	);
    	}
    	return $return;
    }

    //获取所有的专业分类
    public function getMyClass(Request $request,$id){
    	$data = UserClass::where('user_id',$id)->pluck('class_id')->toArray();
    	return response()->json($data);
    }
}
