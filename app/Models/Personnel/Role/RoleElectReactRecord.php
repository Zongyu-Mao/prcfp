<?php

namespace App\Models\Personnel\Role;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\Management\Role\RoleElectReactEvent;

class RoleElectReactRecord extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'user_id','username','elect_id','stand','remark','createtime'
    ];
    // 得到elect
    public function elect(){
        return $this->belongsTo('App\Models\Personnel\Role\RoleElectRecord','elect_id','id');
    }

    //创建
    protected function newRoleElectReact($user_id,$username,$elect_id,$stand,$remark,$createtime) {
        $result = RoleElectReactRecord::create([
            'user_id'   => $user_id,
            'username'   => $username,
            'elect_id'   => $elect_id,
            'stand'   => $stand,
            'remark'   => $remark,
            'createtime'   => $createtime
        ]);
        event(new RoleElectReactEvent($result));
        return $result->id;
    }
}
