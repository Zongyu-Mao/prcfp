<?php

namespace App\Models\Personnel\Role;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\Management\Role\RoleAppliedEvent;

class RoleApplyRecord extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'user_id','role_id','status','remark','createtime'
    ];
    // 一对一关联得到作者
    public function author(){
        return $this->belongsTo('App\Models\User','user_id','id');
    }
    // 一对一关联得到角色
    public function role(){
        return $this->belongsTo('App\Home\Personnel\Role','role_id','id');
    }
    // 一对多处理记录
    public function records(){
        return $this->hasMany('App\Models\Personnel\Role\RoleApplyReactRecord','apply_id','id');
    }

    //创建
    protected function newRoleApply($user_id,$role_id,$status,$remark,$createtime) {
        $result = RoleApplyRecord::create([
            'user_id'   => $user_id,
            'role_id'   => $role_id,
            'status'   => $status,
            'remark'   => $remark,
            'createtime'   => $createtime
        ]);
        event(new RoleAppliedEvent($result));
        return $result->id;
    }
    //更新
    protected function applyUpdate($id,$status) {
        $result = RoleApplyRecord::where('id',$id)->update([
            'status'   => $status
        ]);
        event(new RoleAppliedEvent(RoleApplyRecord::find($id)));
        return $result;
    }
}
