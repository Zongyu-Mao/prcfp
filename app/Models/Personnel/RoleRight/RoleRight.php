<?php

namespace App\Models\Personnel\RoleRight;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleRight extends Model
{
    use HasFactory;
    protected $fillable = [
        'right','role_id','sort','introduction','creator_id'
    ];
    // 一对一关联得到作者
    public function author(){
        return $this->belongsTo('App\Models\User','creator_id','id');
    }
    // 一对一关联得到作者
    public function role(){
        return $this->belongsTo('App\Home\Personnel\Role','role_id','id');
    }

    //创建
    protected function newRoleRight($right,$role_id,$sort,$introduction,$creator_id) {
        $result = RoleRight::create([
            'right'   => $right,
            'role_id'   => $role_id,
            'sort'   => $sort,
            'introduction'   => $introduction,
            'creator_id'   => $creator_id
        ]);
        return $result;
    }
    //更新
    protected function rightUpdate($id,$right,$role_id,$sort,$introduction,$creator_id) {
        $result = RoleRight::where('id',$id)->update([
            'right'   => $right,
            'role_id'   => $role_id,
            'sort'   => $sort,
            'introduction'   => $introduction,
            'creator_id'   => $creator_id
        ]);
        return $result;
    }
}
