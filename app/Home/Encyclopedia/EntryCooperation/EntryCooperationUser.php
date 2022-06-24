<?php

namespace App\Home\Encyclopedia\EntryCooperation;

use Illuminate\Database\Eloquent\Model;
use App\Events\Encyclopedia\EntryCooperation\EntryCooperationMemberJoinedEvent;
use App\Events\Encyclopedia\EntryCooperation\EntryCooperationMemberQuittedEvent;
use App\Events\Encyclopedia\EntryCooperation\EntryCooperationMemberFiredEvent;

class EntryCooperationUser extends Model
{
    protected $fillable = ['cooperation_id','user_id','createtime'];

    public $timestamps = false;

    // 协作组成员的加入
    protected function cooperationMemberJoin($cooperation_id,$user_id,$createtime){
    	$result = EntryCooperationUser::create([
    		'cooperation_id' 	=> $cooperation_id,
    		'user_id' 			=> $user_id,
    		'createtime' 		=> $createtime
    	]);
    	if($result->id){
    		event(new EntryCooperationMemberJoinedEvent($result));
    	}
    	return $result->id ? '1':'0';
    }

    // 请退协作组成员
    protected function cooperationMemberFire($cooperation_id,$fire_id){
        $fireRecord = EntryCooperationUser::where([['cooperation_id',$cooperation_id],['user_id',$fire_id]])->first();
    	$result = EntryCooperationUser::where([['cooperation_id',$cooperation_id],['user_id',$fire_id]])->delete();
        if($result){
            event(new EntryCooperationMemberFiredEvent($fireRecord));
        }
    	return $result ? '1':'0';
    }

    // 成员退出
    protected function cooperationMemberQuit($cooperation_id,$use_id){
        $quitRecord = EntryCooperationUser::where([['cooperation_id',$cooperation_id],['user_id',$use_id]])->first();
        $result = EntryCooperationUser::where([['cooperation_id',$cooperation_id],['user_id',$use_id]])->delete();
        if($result){
            event(new EntryCooperationMemberQuittedEvent($quitRecord));
        }
        return $result ? '1':'0';
    }
}
