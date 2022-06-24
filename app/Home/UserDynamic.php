<?php

namespace App\Home;

use Illuminate\Database\Eloquent\Model;

class UserDynamic extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id','username','behavior','objectName','objectURL','fromName','fromURL','createtime'];

    // 添加用户动态事件
    protected function dynamicAdd($userId,$username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime){
    	$result = UserDynamic::create([
            'user_id'   => $userId,
            'username'  => $username,
            'behavior'  => $behavior,
            'objectName'=> $objectName,
            'objectURL' => $objectURL,
            'fromName' 	=> $fromName,
            'fromURL'	=> $fromURL,
            'createtime'=>$createtime,
        ]);
        return $result->id;
    }
    
}
