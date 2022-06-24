<?php

namespace App\Http\Controllers\Api\Personal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Home\UserDynamic;
use App\Models\User;
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

class PersonalDynamicController extends Controller
{
    //
    public function getDynamics(Request $request){
        $user = auth('api')->user();
        $data = $request->data;
        $id = $data['user_id'];
        
        $pageSize = $data['pageSize'];
        $scope = $data['dynamic_scope'];
        $dynamics = '';
        if($id == $user->id || $id==0) {
        	$id = $user->id;
	        $interest=$user->getInterest->pluck('id')->toArray();
	        array_push($interest,$user->specialty);
	        if($scope==0) {
	        	// 获取好友数据（好友是双向的），如果用户是本人，动态包括好友，如果用户非本人，动态仅针对user_id
		        $friendProsArr = UserFriendRelationship::where('user_id',$id)->pluck('friend_id')->toArray();
		        $friendConsArr = UserFriendRelationship::where('friend_id',$id)->pluck('user_id')->toArray();
		        $friendsArr = array_unique(array_merge($friendProsArr,$friendConsArr));
		        // 关注用户数据
		        $focusUsers = UserFocusRelationship::where('user_id',$id)->pluck('focus_id')->toArray();
		        array_push($friendsArr,$user->id);
		        array_unique(array_merge($friendsArr,$focusUsers));
	        	$dynamics = UserDynamic::whereIn('user_id',$friendsArr)->orderBy('createtime','desc')->paginate($pageSize);
	        } else if ($scope==1) {
	        	$entryArr = Entry::whereIn('cid',$interest)->pluck('id')->toArray();
            	$dynamics = EntryDynamic::whereIn('eid',$entryArr)->orderBy('createtime','desc')->paginate($pageSize);
	        } else if ($scope==2) {
	        	$articleArr = Article::whereIn('cid',$interest)->pluck('id')->toArray();
            	$dynamics = ArticleDynamic::whereIn('aid',$articleArr)->orderBy('createtime','desc')->paginate($pageSize);
	        } else if ($scope==3) {
	        	$examArr = Exam::whereIn('cid',$interest)->pluck('id')->toArray();
            	$dynamics = ExamDynamic::whereIn('exam_id',$examArr)->orderBy('createtime','desc')->paginate($pageSize);
	        } else if ($scope==4) {
	        	$groupArr = Group::whereIn('cid',$interest)->pluck('id')->toArray();
            	$dynamics = GroupDynamic::whereIn('gid',$groupArr)->orderBy('createtime','desc')->paginate($pageSize);
	        }
        } else {
        	$dynamics = UserDynamic::where('user_id',$id)->orderBy('createtime','desc')->paginate($pageSize);
        }
        return ['dynamics'=>$dynamics];
    }
}
