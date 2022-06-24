<?php

namespace App\Home\Personal\Relationship;

use Illuminate\Database\Eloquent\Model;
use App\Events\Personal\Relationship\FriendActivityInvitationCreatedEvent;
use App\Events\Personal\Relationship\FriendActivityInvitationRepliedEvent;

class FriendActivityInvitationRecord extends Model
{
    protected $fillable = ['user_id','username','invite_id','invite_username','subject','remark','type','type_id','inviteResult','invitationLink'];

    // 记录本次邀请
    protected function friendActivityInvitationCreate($user_id,$username,$invite_id,$invite_username,$subject,$remark,$type,$type_id,$inviteResult,$invitationLink) {
        $result = FriendActivityInvitationRecord::create([
            'user_id'   => $user_id,
            'username'	=> $username,
            'invite_id'			=> $invite_id,
            'invite_username'	=> $invite_username,
            'subject'		=> $subject,
            'remark'		=> $remark,
            'inviteResult'	=> $inviteResult,
            'type'			=> $type,
            'type_id'		=> $type_id,
            'invitationLink'=> $invitationLink,
        ]);
        // 申请写入成功，触发申请事件
        if($result->id){
        	event(new FriendActivityInvitationCreatedEvent($result));
        }
        return $result->id;
    }

    // 用户同意或拒绝本次邀请
    protected function friendActivityInvitationReply($id,$reply) {
        $result = FriendActivityInvitationRecord::where('id',$id)->update([
            'inviteResult'	=> $reply
        ]);
        // 申请写入成功，触发申请事件
        if($result){
        	event(new FriendActivityInvitationRepliedEvent(FriendActivityInvitationRecord::find($id)));
        }
        return $result ? '1':'0';
    }
}
