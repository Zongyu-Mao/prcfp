<?php

namespace App\Models\Personal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateMedalRecord extends Model
{
    use HasFactory;
    protected $fillable = ['medal_id','user_id','createtime'];
    public $timestamps = false;

    // 新建记录
    protected function privateMedalRecordAdd($medal_id,$user_id,$createtime){
    	$result = PrivateMedalRecord::create([
    		'medal_id'	=> $medal_id,
			'user_id'	=> $user_id,
			'createtime'	=> $createtime
    	]);
    	// 写入之后要添加判断事件，判断流程是否可以结束
    	// event(new PunishRecordAddedEvent($result));
    	return $result->id;
    }
}
