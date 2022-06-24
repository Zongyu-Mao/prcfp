<?php

namespace App\Models\Committee\Surveillance\Mark;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarkDisposeWayEvent extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['wid','user_id','username','content','createtime'];
    // 一对一关联组长信息

    //新增协作动态的函数
    protected function disposeEventAdd($wid,$userid,$username,$content,$createtime){
    	$result = MarkDisposeWayEvent::create([
    		'wid'	=> $wid,
            'user_id'   => $userid,
    		'username'	=> $username,
    		'content'	=> $content,
    		'createtime'	=> $createtime,
    		]);
    	return $result->id;
    }
}
