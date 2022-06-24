<?php

namespace App\Http\Controllers\Api\Personal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleCooperation;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamCooperation;
use App\Home\Organization\Group;
use App\Home\Personal\Relationship\UserFriendRelationship;
use App\Home\Personal\Relationship\FriendActivityInvitationRecord;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
    public function getFriends(Request $request) {
        $id = $request->cooperation_id;
        // 这里的scope还没有做，只给了词条的
    	$scope = $request->scope;
    	$selfId = auth('api')->user()->id;
    	// 获取好友数据（好友是双向的）
        $friendProsArr = UserFriendRelationship::where('user_id',$selfId)->pluck('friend_id')->toArray();
        $friendConsArr = UserFriendRelationship::where('friend_id',$selfId)->pluck('user_id')->toArray();
        $friendsArr = array_unique(array_merge($friendProsArr,$friendConsArr));
        if(!count($friendsArr)){
            return $arr = [
                'friends'=>[],
                'crewArr'=>[],
                'invitationRecords'=>[]
            ];
        }
        $cooperation = ($scope==1?EntryCooperation::find($id):($scope==2?ArticleCooperation::find($id):($scope==3?ExamCooperation::find($id):($scope==4?Group::find($id):''))));
        $content = ($scope==1?Entry::find($cooperation->eid):($scope==2?Article::find($cooperation->aid):($scope==3?Exam::find($cooperation->exam_id):($scope==4?$cooperation:''))));
        $crewArr = $cooperation->crews()->pluck('user_id')->toArray();
        array_push($crewArr,$content->manage_id);
        array_unique($crewArr);
        // 得到有关协作计划的邀请记录
        $invitationRecords = FriendActivityInvitationRecord::where([['type',$scope],['type_id',$id],['inviteResult',0]])->pluck('invite_id')->toArray();
        $invitationRecords = array_unique($invitationRecords);
        
        for($i=0; $i<count($friendsArr); $i++) {
            if(in_array($friendsArr[$i], $invitationRecords) || in_array($friendsArr[$i], $crewArr)) {
                $friendsArr[$i]='';
                // unset($friendsArr[$i]);//不适用unset，会使序号混乱
            }
        }
        $friendsArr = array_filter($friendsArr);

        $friends = User::whereIn('id',$friendsArr)->get();

        return $arr = [
            'friendsArr'=>$friendsArr,
			'friends'=>$friends,
			'crewArr'=>$crewArr,
			'invitationRecords'=>$invitationRecords
        ];
    }

    public function getBasicInviteRecord(Request $request) {
        // 此处结果是为了在申请加入之前看看自己有没有别邀请过或者之前有没有申请记录
        $data = $request->data;
        $id = $data['cooperation_id'];
        $scope = $data['scope'];
        $user = Auth::user();
        $selfId = $user->id;
        $inviteRecord = FriendActivityInvitationRecord::where([['type',$scope],['type_id',$id],['inviteResult',0],['invite_id',$selfId]])->first();

        return $arr = [
            'inviteRecord'=>$inviteRecord,
        ];
    }

    // 得到我的所有好友（推举）
    public function getAllFriends(Request $request) {
        // 此处id也是user_id
        $data = $request->data;
        $id = $data['user_id'];
        $user = auth('api')->user();
        $selfId = $id;
        $friendProsArr = UserFriendRelationship::where('user_id',$selfId)->pluck('friend_id')->toArray();
        $friendConsArr = UserFriendRelationship::where('friend_id',$selfId)->pluck('user_id')->toArray();
        $friendsArr = array_unique(array_merge($friendProsArr,$friendConsArr));
        $friends = User::whereIn('id',$friendsArr)->with('getRole')->with('getCommittee')->get();
        return $arr = [
            'friends'=>$friends
        ];
    }
}
