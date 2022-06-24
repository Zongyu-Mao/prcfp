<?php

namespace App\Home\Personnel\Level;

use Illuminate\Database\Eloquent\Model;
use App\Home\Personnel\Level;

class UserLevel extends Model
{
    protected $fillable = ['user_id','level_id','status'];

    public $timestamps = true;

    //一对一关联等级表，获取等级
    public function getLevel(){
        return $this->belongsTo('App\Home\Personnel\Level','level_id','id');
    }

    // 用户等级初始化
    protected function levelInitialization($user_id,$grow){
        $level_id = Level::where([['creditslower','<=',$grow],['creditshigher','>=',$grow]])->first()->id;
        $result = $this->levelCreate($user_id,$level_id,1);
        return $result;
    }

    // 新建角色等级记录
    protected function levelCreate($user_id,$level_id,$status) {
        $result = UserLevel::create([
            'user_id'   => $user_id,
            'level_id' 	=> $level_id,
            'status'=> $status
        ]);
        return $result;
    }
}
