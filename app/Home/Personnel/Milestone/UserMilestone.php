<?php

namespace App\Home\Personnel\Milestone;

use Illuminate\Database\Eloquent\Model;

class UserMilestone extends Model
{
    protected $fillable = ['id','user_id','milestone_id','status'];

    public $timestamps = true;

    // 新建角色里程碑记录
    protected function milestoneCreate($user_id,$milestone_id,$status) {
        $result = UserMilestone::create([
            'user_id'   => $user_id,
            'milestone_id' 	=> $milestone_id,
            'status'	=> $status
        ]);
        return $result->id;
    }
}
