<?php

namespace App\Http\Controllers\Api\Cooperation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleCooperation;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamCooperation;
use App\Home\Personal\Relationship\FriendActivityInvitationRecord;
use App\Home\Organization\Group;
use App\Home\Organization\Group\GroupUser;
use App\Home\Personal\Relationship\UserFriendRelationship;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CooperationMemberController extends Controller
{
    // 邀请成员到协作计划
    public function cooperationMemberInvite(Request $request){
    	$id = $request->cooperation_id;	//该id是cooperation的id
    	$scope = $request->scope;	//该id是cooperation的id
    	$selfId = auth('api')->user()->id;
    	$username = auth('api')->user()->username;
    	$invite_id = $request->invite_id;
    	$invite_username = User::find($invite_id)->username;
    	$remark = $request->remark;
    	$result = false;
        // 一通操作
    	if($scope==1){
    		$cooperation = EntryCooperation::find($id);
       		$entry = Entry::find($cooperation->eid);
    		$subject = '加入词条《'.$entry->title.'》协作计划：<'.$cooperation->title.'>。';
	    	$type = 1;
	    	$type_id = $id;
	    	$inviteResult = '0';
	    	$invitationLink = '/encyclopedia/cooperation/'.$entry->id.'/'.$entry->title;
	    	$result = FriendActivityInvitationRecord::friendActivityInvitationCreate($selfId,$username,$invite_id,$invite_username,$subject,$remark,$type,$type_id,$inviteResult,$invitationLink);
    	}elseif($scope==2){
    		$cooperation = ArticleCooperation::find($id);
        	$article = Article::find($cooperation->aid);
        	$subject = '加入著作《'.$article->title.'》协作计划：<'.$cooperation->title.'>。';
        	$type = 2;
        	$type_id = $id;
        	$inviteResult = '0';
        	$invitationLink = '/publication/cooperation/'.$article->id.'/'.$article->title;
    		$result = FriendActivityInvitationRecord::friendActivityInvitationCreate($selfId,$username,$invite_id,$invite_username,$subject,$remark,$type,$type_id,$inviteResult,$invitationLink);
    	}elseif($scope==3){
            $cooperation = ExamCooperation::find($id);
            $exam = Exam::find($cooperation->exam_id);
            $subject = '加入试卷《'.$exam->title.'》协作计划：<'.$cooperation->title.'>。';
            $type = 3;
            $type_id = $id;
            $inviteResult = '0';
            $invitationLink = '/examination/cooperation/'.$exam->id.'/'.$exam->title;
            $result = FriendActivityInvitationRecord::friendActivityInvitationCreate($selfId,$username,$invite_id,$invite_username,$subject,$remark,$type,$type_id,$inviteResult,$invitationLink);
        }elseif($scope==4){
            $group = Group::find($id);
            $subject = '加入组织《'.$group->title.'》。';
            $type = 4;
            $type_id = $id;
            $inviteResult = 0;
            $invitationLink = '/organization/group/'.$group->id.'/'.$group->title;
            $result = FriendActivityInvitationRecord::friendActivityInvitationCreate($selfId,$username,$invite_id,$invite_username,$subject,$remark,$type,$type_id,$inviteResult,$invitationLink);
        }
    	return ['success'=>$result? true:false];
    }
}
