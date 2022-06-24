<?php

namespace App\Home\Personal\Relationship;

use Illuminate\Database\Eloquent\Model;

class UserFriendRelationship extends Model
{
    protected $fillable = ['user_id','friend_id','createtime'];
    public $timestamps = false;

    // 新建好友关系
    protected function friendRelationshipAdd($user_id,$friend_id,$createtime) {
        $result = UserFriendRelationship::create([
            'user_id'   => $user_id,
            'friend_id'	=> $friend_id,
            'createtime'=>$createtime,
        ]);
        return $result->id ? '1':'0';
    }

    // 解除好友关系
    protected function friendRelationshipRelieve($user_id,$friend_id) {
        if(UserFriendRelationship::where([['user_id',$user_id],['friend_id',$friend_id]])->exists()){
            $result = UserFriendRelationship::where([['user_id',$user_id],['friend_id',$friend_id]])->delete();
        }elseif(UserFriendRelationship::where([['user_id',$friend_id],['friend_id',$user_id]])->exists()){
            $result = UserFriendRelationship::where([['user_id',$friend_id],['friend_id',$user_id]])->delete();
        }
        return $result ? '1':'0';
    }
}
