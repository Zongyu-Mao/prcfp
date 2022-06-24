<?php

namespace App\Home\Organization\Group;

use Illuminate\Database\Eloquent\Model;

class GroupFocusUser extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id','gid'];

    // 用户关注
    protected function groupFocus($user_id,$gid){
    	$result = GroupFocusUser::create([
    		'user_id'	=> $user_id,
    		'gid'	=> $gid
    	]);
    	return $result->id;
    }

    // 用户取消关注
    protected function groupFocusCancel($user_id,$gid){
    	$result = GroupFocusUser::where([['user_id',$user_id],['gid',$gid]])->delete();
    	return $result;
    }
}
