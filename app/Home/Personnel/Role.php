<?php

namespace App\Home\Personnel;

use Illuminate\Database\Eloquent\Model;
use App\Events\Personnel\Role\RoleModifiedEvent;

class Role extends Model
{
	protected $fillable = ['role','sort','introduction','creditslower','creator_id','power_level'];
    public $timestamps = true;

    // 角色的更改一般属于后台操作，现在不设置事件
    // event
	//用户角色的写入
    protected function roleAdd($rolename,$sort,$creditslower,$introduction,$creator_id,$power_level) {
        $result = Role::create([
            'role'   	=> $rolename,
            'sort'      => $sort,
            'creditslower'	=> $creditslower,
            'introduction'   => $introduction,
            'creator_id'   => $creator_id,
            'power_level'   => $power_level,
        ]);
        event(new RoleModifiedEvent($result));
        return $result->id;
    }

    //用户角色的属性修改
    protected function roleModify($id,$rolename,$sort,$creditslower,$introduction,$power_level) {
        $result = Role::where('id',$id)->update([
            'role'      => $rolename,
            'sort'   	=> $sort,
            'creditslower'  => $creditslower,
            'introduction'  => $introduction,
            'power_level'	=> $power_level,
        ]);
        event(new RoleModifiedEvent(Role::find($id)));
        return $result;
    }

    //用户角色的删除
    protected function roleDelete($id) {
        $result = Role::where('id',$id)->delete();
        // event(new RoleModifiedEvent(Role::find($id))); //删除暂不列入事件
        return $result;
    }
}
