<?php

namespace App\Home\Personnel\Role;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $fillable = ['user_id','role_id','createtime'];

    //一对一关联角色表，获取角色
    public function getRole(){
        return $this->hasOne('App\Home\Personnel\Role','id','role_id');
    }

    // 新建角色记录
    protected function roleCreate($user_id,$role_id,$createtime) {
        $result = UserRole::create([
            'user_id'   => $user_id,
            'role_id' 	=> $role_id,
            'createtime'=> $createtime
        ]);
        return $result->id;
    }

    // 修改角色记录
    protected function roleModify($user_id,$new_role_id) {
        $result = UserRole::where('user_id',$user_id)->update([
            'role_id'   => $new_role_id
        ]);
        return $result->id;
    }
}
