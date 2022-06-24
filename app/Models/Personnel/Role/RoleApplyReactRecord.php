<?php

namespace App\Models\Personnel\Role;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\Management\Role\RoleApplyReactEvent;

class RoleApplyReactRecord extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'user_id','username','apply_id','stand','remark','createtime'
    ];

    // 得到apply
    public function apply(){
        return $this->belongsTo('App\Models\Personnel\Role\RoleApplyRecord','apply_id','id');
    }
    //创建
    protected function newRoleApplyReact($user_id,$username,$apply_id,$stand,$remark,$createtime) {
        $result = RoleApplyReactRecord::create([
            'user_id'   => $user_id,
            'username'   => $username,
            'apply_id'   => $apply_id,
            'stand'   => $stand,
            'remark'   => $remark,
            'createtime'   => $createtime
        ]);
        event(new RoleApplyReactEvent($result));
        return $result->id;
    }
}
