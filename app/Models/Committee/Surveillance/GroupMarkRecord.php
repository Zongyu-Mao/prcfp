<?php

namespace App\Models\Committee\Surveillance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMarkRecord extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'mark_id','user_id','stand','remark','createtime'
    ];

    // 关联取得操作用户信息
    public function getOperator(){
        return $this->belongsTo('App\Models\User','user_id','id');
    }

    //创建
    protected function newMarkReactRecord($mark_id,$user_id,$stand,$remark,$createtime) {
        $result = GroupMarkRecord::create([
            'mark_id'   => $mark_id,
            'user_id'   => $user_id,
            'stand'   => $stand,
            'remark'   => $remark,
            'createtime'   => $createtime
        ]);
        // event(new VoteCreatedEvent($result));
        return $result->id;
    }
}
