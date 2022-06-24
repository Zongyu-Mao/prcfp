<?php

namespace App\Home\Publication\ArticleCooperation;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\ArticleCooperation\ArticleCooperationMemberJoinedEvent;
use App\Events\Publication\ArticleCooperation\ArticleCooperationMemberFiredEvent;
use App\Events\Publication\ArticleCooperation\ArticleCooperationMemberQuittedEvent;

class ArticleCooperationUser extends Model
{
    protected $fillable = ['cooperation_id','user_id','createtime'];

    public $timestamps = false;

    // 协作组成员的加入
    protected function cooperationMemberJoin($cooperation_id,$user_id,$createtime){
    	$result = ArticleCooperationUser::create([
    		'cooperation_id' 	=> $cooperation_id,
    		'user_id' 			=> $user_id,
    		'createtime' 		=> $createtime
    	]);
    	if($result->id){
    		event(new ArticleCooperationMemberJoinedEvent($result));
    	}
    	return $result->id ? '1':'0';
    }

    // 请退协作组成员
    protected function cooperationMemberFire($cooperation_id,$fire_id){
        $fireRecord = ArticleCooperationUser::where([['cooperation_id',$cooperation_id],['user_id',$fire_id]])->first();
    	$result = ArticleCooperationUser::where([['cooperation_id',$cooperation_id],['user_id',$fire_id]])->delete();
        if($result){
            event(new ArticleCooperationMemberFiredEvent($fireRecord));
        }
    	return $result ? '1':'0';
    }

    // 成员退出
    protected function cooperationMemberQuit($cooperation_id,$use_id){
        $quitRecord = ArticleCooperationUser::where([['cooperation_id',$cooperation_id],['user_id',$use_id]])->first();
        $result = ArticleCooperationUser::where([['cooperation_id',$cooperation_id],['user_id',$use_id]])->delete();
        if($result){
            event(new ArticleCooperationMemberQuittedEvent($quitRecord));
        }
        return $result ? '1':'0';
    }
}
