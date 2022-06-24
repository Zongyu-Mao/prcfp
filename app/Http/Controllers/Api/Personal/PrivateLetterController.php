<?php

namespace App\Http\Controllers\Api\Personal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personal\Relationship\UserFriendApplicationRecord;
use App\Home\Personal\Relationship\FriendActivityInvitationRecord;
use App\Home\Personal\Relationship\FriendPrivateLetter;
use App\Home\Personal\Relationship\UserFriendRelationship;
use App\Home\UserDynamic;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PrivateLetterController extends Controller
{
    //显示私信主页
    public function privateLetter(){
    	$user_id = auth('api')->user()->id;
    	// 返回私信数据1：好友申请
    	$friendApplications = UserFriendApplicationRecord::where('user_id',$user_id)->orderBy('created_at','desc')->get();
    	// 返回私信数据2：好友私信
    	$mySendFriendLetters = FriendPrivateLetter::where([['from_id',$user_id],['pid','0']])->orderBy('created_at','desc')->get();
    	$myReplyFriendLetters = FriendPrivateLetter::where([['from_id',$user_id],['pid','>','0']])->get();
    	$sendToMeFriendLetters = FriendPrivateLetter::where([['to_id',$user_id],['pid','0']])->orderBy('created_at','desc')->get();
    	$replyToMeFriendLetters = FriendPrivateLetter::where([['to_id',$user_id],['pid','>','0']])->get();
        // 这里可以直接关联查询pid 一次带过。注意优化
    	$FriendLetters = array(
    		'mySendFriendLetters' 	=> $mySendFriendLetters,
    		'myReplyFriendLetters' 	=> $myReplyFriendLetters,
    		'sendToMeFriendLetters' => $sendToMeFriendLetters,
    		'replyToMeFriendLetters' => $replyToMeFriendLetters
    		);
        // 返回私信数据3：活动邀请
        $friendActivityInvitations = FriendActivityInvitationRecord::where('invite_id',$user_id)->orderBy('created_at','desc')->get();
        return $data = array(
        	'friendApplications' => $friendApplications,
        	'FriendLetters' => $FriendLetters,
        	'friendActivityInvitations' => $friendActivityInvitations
        );
    }

    // 这是获取分项站内信
    public function privatePartLetters(Request $request){
        $user_id = auth('api')->user()->id;
        $data = $request->data;
        $pageSize = $data['pageSize'];
        $attr = $data['attr_scope'];
        $ls = '';
        if($attr == 'invitation') {
            $ls = FriendActivityInvitationRecord::where('invite_id',$user_id)->orderBy('created_at','desc')->paginate($pageSize);
        } else if($attr == 'lettersIn') {
            $ls = FriendPrivateLetter::where([['to_id',$user_id],['pid','0']])->orderBy('created_at','desc')->with('reply')->paginate($pageSize);
        } else if($attr == 'lettersOut') {
            $ls = FriendPrivateLetter::where([['from_id',$user_id],['pid','0']])->orderBy('created_at','desc')->with('reply')->paginate($pageSize);
        } else if($attr == 'application') {
            $ls = UserFriendApplicationRecord::where('user_id',$user_id)->orderBy('created_at','desc')->paginate($pageSize);
        } else if($attr == 'system') {
            $ls = [];
        }
        return ['letters'=>$ls];
    }

    //给用户发送私信以及解除好友
    public function privateLetterSend(Request $request){
    	$id = $request->id;
    	$start = $request->start;
    	$title = $request->get('title');
		$content = $request->get('letter');
        $me = auth('api')->user();
        $back = '';
		if($start == 0){
			// 0是首发送私信
			$username = $request->name;
			$pid = 0;
			$result = FriendPrivateLetter::friendPrivateLetterSend($me->id,$me->username,$id,$username,$title,$content,$pid);
		}elseif($start == 1){
			// 1是回复私信
			$letter = FriendPrivateLetter::find($id);
			$pid = $id;
            $user_id = $me->id;
			$result = FriendPrivateLetter::friendPrivateLetterSend($me->id,$me->username,$letter->from_id,$letter->from_username,$title,$content,$pid);
            $mySendFriendLetters = FriendPrivateLetter::where([['from_id',$user_id],['pid','0']])->orderBy('created_at','desc')->get();
            $myReplyFriendLetters = FriendPrivateLetter::where([['from_id',$user_id],['pid','>','0']])->get();
            $sendToMeFriendLetters = FriendPrivateLetter::where([['to_id',$user_id],['pid','0']])->orderBy('created_at','desc')->get();
            $replyToMeFriendLetters = FriendPrivateLetter::where([['to_id',$user_id],['pid','>','0']])->get();
            // 这里可以直接关联查询pid 一次带过。注意优化
            $back = array(
                'mySendFriendLetters'   => $mySendFriendLetters,
                'myReplyFriendLetters'  => $myReplyFriendLetters,
                'sendToMeFriendLetters' => $sendToMeFriendLetters,
                'replyToMeFriendLetters' => $replyToMeFriendLetters
                );
		}elseif($start==2){
			// 这里特殊操作，要同时解除好友
			$username = $request->name;
            $user_id = $me->id;
			$pid = 0;
        	FriendPrivateLetter::friendPrivateLetterSend($me->id,$me->username,$id,$username,$title,$content,$pid);
        	$result = UserFriendRelationship::friendRelationshipRelieve($id,$me->id);
            if($result) {
                // 获取好友数据（好友是双向的）
                $friendProsArr = UserFriendRelationship::where('user_id',$user_id)->pluck('friend_id')->toArray();
                $friendConsArr = UserFriendRelationship::where('friend_id',$user_id)->pluck('user_id')->toArray();
                $friendsArr = array_unique(array_merge($friendProsArr,$friendConsArr));
                $friends = User::whereIn('id',$friendsArr)->with('getAvatar')->get();
                $dynamics = UserDynamic::whereIn('user_id',$friendsArr)->limit(30)->orderBy('createtime','desc')->get();
                $back = ['friendsArr'=>$friendsArr,'friends'=>$friends,'dynamics'=>$dynamics];
            }
        } elseif($start==3) {
            //$id要添加的用户,添加成功是不需要返回$back的，只需要前端判断改一下
            $user = User::find($id);//申请对象
            $user_id = $user->id;
            $application_id = $me->id;//自己
            if($user && $application_id != $user_id){
                $username = $user->username;
                $application_username = $me->username;
                $applyResult = 0;
                // 两重判断，如果双方有任何一方有过添加记录，无法继续添加好友行为emmmm，想了想，还是先单方判定，毕竟被申请方全力剥夺貌似有点过分，再考虑考虑
                //  || !UserFriendApplicationRecord::where([['user_id',$application_id],['application_id',$user_id]]->exists()
                if(!UserFriendApplicationRecord::where([['user_id',$user_id],['application_id',$application_id],['applyResult',0]])->exists()){
                    $createtime = Carbon::now();
                    $result = UserFriendApplicationRecord::friendApplicationRecord($user_id,$username,$application_id,$application_username,$title,$content,$applyResult);
                }
            }
        }
    	
    	return [
    		'success'	=> $result? true:false,'back'=>$back
    	];	
    
    }

}
