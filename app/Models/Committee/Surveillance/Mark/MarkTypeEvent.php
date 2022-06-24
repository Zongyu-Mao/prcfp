<?php

namespace App\Models\Committee\Surveillance\Mark;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarkTypeEvent extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['tid','user_id','username','content','createtime'];
    // 一对一关联组长信息

    //新增协作动态的函数
    protected function typeEventAdd($tid,$userid,$username,$content,$createtime){
    	$result = MarkTypeEvent::create([
    		'tid'	=> $tid,
            'user_id'   => $userid,
    		'username'	=> $username,
    		'content'	=> $content,
    		'createtime'	=> $createtime,
    		]);
    	return $result->id;
    }
}
