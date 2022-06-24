<?php

namespace App\Home\Organization\Group;

use Illuminate\Database\Eloquent\Model;
use App\Events\Organization\Group\Member\GroupMemberJoinedEvent;
use App\Events\Organization\Group\Member\GroupMemberFiredEvent;
use App\Events\Organization\Group\Member\GroupMemberQuittedEvent;
use App\Events\Organization\Group\Member\GroupMemberPositionChangedEvent;

class GroupUser extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id','gid','position','createtime'];
    // 新增用户,position with default(1)
    protected function groupMemberJoin($user_id,$gid,$createtime){
    	$result = GroupUser::create([
            'user_id'   => $user_id,
            'gid'		=> $gid,
            'createtime'=>$createtime
        ]);
        if($result->id){
    		event(new GroupMemberJoinedEvent($result));
    	}
        return $result->id;
    }

    // 关联user_id与user表获取用户信息
    public function getGroupUserInfo(){
        return $this->hasOne('App\Models\User','id','user_id');
    }

    // 请退成员
    protected function groupMemberFire($gid,$fire_id){
        $fireRecord = GroupUser::where([['gid',$gid],['user_id',$fire_id]])->first();
    	$result = GroupUser::where([['gid',$gid],['user_id',$fire_id]])->delete();
        if($result){
            event(new GroupMemberFiredEvent($fireRecord));
        }
    	return $result ? '1':'0';
    }

    // 成员退出
    protected function groupMemberQuit($gid,$use_id){
        $quitRecord = GroupUser::where([['gid',$gid],['user_id',$use_id]])->first();
        $result = GroupUser::where([['gid',$gid],['user_id',$use_id]])->delete();
        if($result){
            event(new GroupMemberQuittedEvent($quitRecord));
        }
        return $result ? '1':'0';
    }

    // 变更成员位置
    protected function groupMemberPositionChange($gid,$use_id,$position){
        $result = GroupUser::where([['gid',$gid],['user_id',$use_id]])->update(['position'=>$position]);
        $changeRecord = GroupUser::where([['gid',$gid],['user_id',$use_id]])->first();
        if($result){
            event(new GroupMemberPositionChangedEvent($changeRecord));
        }
        return $result ? '1':'0';
    }
}
