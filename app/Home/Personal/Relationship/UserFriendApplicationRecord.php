<?php

namespace App\Home\Personal\Relationship;

use Illuminate\Database\Eloquent\Model;
use App\Events\Personal\Relationship\UserFriendApplicationCreatedEvent;
use App\Events\Personal\Relationship\UserFriendApplicationAgreedEvent;
use App\Events\Personal\Relationship\UserFriendApplicationRejectedEvent;

class UserFriendApplicationRecord extends Model
{
    protected $fillable = ['user_id','username','application_id','application_username','title','content','applyResult'];

    // 添加好友申请记录
    protected function friendApplicationRecord($user_id,$username,$application_id,$application_username,$title,$content,$applyResult) {
        $result = UserFriendApplicationRecord::create([
            'user_id'   => $user_id,
            'username'	=> $username,
            'application_id'		=> $application_id,
            'application_username'	=> $application_username,
            'title'		=> $title,
            'content'	=> $content,
            'applyResult'	=> $applyResult,
        ]);
        // 申请写入成功，触发申请事件
        if($result->id){
        	event(new UserFriendApplicationCreatedEvent($result));
        }
        return $result->id ? '1':'0';
    }

    // 同意或拒绝好友申请
    protected function friendApplicationStand($id,$stand) {
        $result = UserFriendApplicationRecord::where('id',$id)->update([
            'applyResult'   => $stand,
        ]);
        // 申请写入成功，触发申请事件
        if($result){
            if($stand==1){
                event(new UserFriendApplicationAgreedEvent(UserFriendApplicationRecord::find($id)));
            }elseif($stand==2){
                event(new UserFriendApplicationRejectedEvent(UserFriendApplicationRecord::find($id)));
            } 
        }
        return $result ? '1':'0';
    }
}
