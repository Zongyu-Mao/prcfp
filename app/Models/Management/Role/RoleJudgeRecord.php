<?php

namespace App\Models\Management\Role;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\Management\Role\RoleJudgedEvent;

class RoleJudgeRecord extends Model
{
    use HasFactory;
    protected $fillable = ['role_id','user_id','handle_id','createtime'];
    public $timestamps = false;

    // 新建记录
    protected function recordAdd($role_id,$user_id,$handle_id,$createtime){
    	$result = RoleJudgeRecord::create([
    		'role_id'	=> $role_id,
			'user_id'	=> $user_id,
			'handle_id'	=> $handle_id,
			'createtime'	=> $createtime
    	]);
    	// 写入之后要添加判断事件，判断流程是否可以结束
    	event(new RoleJudgedEvent($result));
    	return $result->id;
    }
}
