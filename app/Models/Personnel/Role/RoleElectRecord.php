<?php

namespace App\Models\Personnel\Role;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\Management\Role\RoleElectedEvent;

class RoleElectRecord extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'user_id','elect_id','role_id','status','remark','createtime'
    ];
    // 一对一关联得到作者
    public function author(){
        return $this->belongsTo('App\Models\User','user_id','id');
    }
    // 一对一关联得到作者
    public function elector(){
        return $this->belongsTo('App\Models\User','elect_id','id');
    }
    // 一对一关联得到角色
    public function role(){
        return $this->belongsTo('App\Home\Personnel\Role','role_id','id');
    }
    // 一对多处理记录
    public function records(){
        return $this->hasMany('App\Models\Personnel\Role\RoleElectReactRecord','elect_id','id');
    }

    //创建
    protected function newRoleElect($user_id,$elect_id,$role_id,$status,$remark,$createtime) {
        $result = RoleElectRecord::create([
            'user_id'   => $user_id,
            'elect_id'   => $elect_id,
            'role_id'   => $role_id,
            'status'   => $status,
            'remark'   => $remark,
            'createtime'   => $createtime
        ]);
        event(new RoleElectedEvent($result));
        return $result->id;
    }
    //更新
    protected function electUpdate($id,$status) {
        $result = RoleElectRecord::where('id',$id)->update([
            'status'   => $status
        ]);
        event(new RoleElectedEvent(RoleElectRecord::find($id)));
        return $result;
    }
}
