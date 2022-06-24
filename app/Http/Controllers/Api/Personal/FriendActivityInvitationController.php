<?php

namespace App\Http\Controllers\Api\Personal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personal\Relationship\FriendActivityInvitationRecord;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleCooperation;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamCooperation;
use App\Home\Organization\Group;

class FriendActivityInvitationController extends Controller
{
    //同意好友的协作邀请
    public function friendActivityInvitation(Request $request){
    	// 判断，如果要加入对象协作计划有效对应，继续操作

        $id = $request->id;
    	$stand = $request->stand;
        $handle_id = $request->user_id;
    	$result = false;
    	$invitation = FriendActivityInvitationRecord::find($id);
    	if($stand==1 && $handle_id==$invitation->invite_id){
            $reply=1;
            if($invitation->type == 1){
                $cooperation = EntryCooperation::find($invitation->type_id);
                $cooperation_parent = Entry::find($cooperation->eid);
                if($cooperation_parent->cooperation_id == $cooperation->id){
                    // 对应了词条和协作计划，改变邀请记录状态
                    $result = FriendActivityInvitationRecord::friendActivityInvitationReply($id,$reply);
                }
            }elseif($invitation->type == 2){
                $cooperation = ArticleCooperation::find($invitation->type_id);
                $cooperation_parent = Article::find($cooperation->aid);
                if($cooperation_parent->cooperation_id == $cooperation->id){
                    $result = FriendActivityInvitationRecord::friendActivityInvitationReply($id,$reply);
                }
            }elseif($invitation->type == 3){
                $cooperation = ExamCooperation::find($invitation->type_id);
                $cooperation_parent = Exam::find($cooperation->exam_id);
                if($cooperation_parent->cooperation_id == $cooperation->id){
                    return $result = FriendActivityInvitationRecord::friendActivityInvitationReply($id,$reply);
                }
            }elseif($invitation->type == 4){
                $group = Group::find($invitation->type_id);
                if($group->id){
                    $result = FriendActivityInvitationRecord::friendActivityInvitationReply($id,$reply);
                }
            }
        }elseif($stand==2 && $handle_id==$invitation->invite_id){
            $reply = 2;
            // 拒绝比较简单，仅需写入状态，不需要调整其余模型的数据
            $result = FriendActivityInvitationRecord::friendActivityInvitationReply($id,$reply);
        }
    	return [
    		'success'	=> $result? true:false
    	];
    }

}
