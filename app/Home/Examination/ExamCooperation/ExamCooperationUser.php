<?php

namespace App\Home\Examination\ExamCooperation;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\ExamCooperation\Member\MemberJoinedEvent;
use App\Events\Examination\ExamCooperation\Member\MemberFiredEvent;
use App\Events\Examination\ExamCooperation\Member\MemberQuittedEvent;

class ExamCooperationUser extends Model
{
    protected $fillable = ['cooperation_id','user_id','createtime'];

    public $timestamps = false;

    // 协作组成员的加入
    protected function cooperationMemberJoin($cooperation_id,$user_id,$createtime){
    	$result = ExamCooperationUser::create([
    		'cooperation_id' 	=> $cooperation_id,
    		'user_id' 			=> $user_id,
    		'createtime' 		=> $createtime
    	]);
    	if($result->id){
    		event(new MemberJoinedEvent($result));
    	}
    	return $result->id ? '1':'0';
    }

    // 请退协作组成员
    protected function cooperationMemberFire($cooperation_id,$fire_id){
        $fireRecord = ExamCooperationUser::where([['cooperation_id',$cooperation_id],['user_id',$fire_id]])->first();
    	$result = ExamCooperationUser::where([['cooperation_id',$cooperation_id],['user_id',$fire_id]])->delete();
        if($result){
            event(new MemberFiredEvent($fireRecord));
        }
    	return $result ? '1':'0';
    }

    // 成员退出
    protected function cooperationMemberQuit($cooperation_id,$use_id){
        $quitRecord = ExamCooperationUser::where([['cooperation_id',$cooperation_id],['user_id',$use_id]])->first();
        $result = ExamCooperationUser::where([['cooperation_id',$cooperation_id],['user_id',$use_id]])->delete();
        if($result){
            event(new MemberQuittedEvent($quitRecord));
        }
        return $result ? '1':'0';
    }
}
