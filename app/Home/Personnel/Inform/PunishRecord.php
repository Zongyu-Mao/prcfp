<?php

namespace App\Home\Personnel\Inform;

use Illuminate\Database\Eloquent\Model;

class PunishRecord extends Model
{
    protected $fillable = ['medal_id','punish_id','inform_id','type','endtime','createtime','status'];
    public $timestamps = false;

    // 关联medal，表里的type是指inform的类型，1是basic2是judgement3是message，而区分惩戒与惩示，是inform_id是否为0，type是否大于3
    public function medal(){
        return $this->belongsTo('App\Home\Personnel\Medal','medal_id','id');
    }

    // 新建记录
    protected function punishRecordAdd($medal_id,$punish_id,$inform_id,$type,$endtime,$createtime,$status=1){
    	$result = PunishRecord::create([
    		'medal_id'	=> $medal_id,
			'punish_id'	=> $punish_id,
			'inform_id' => $inform_id,
			'type'	    => $type,
			'endtime'	=> $endtime,
            'createtime'    => $createtime,
			'status'	=> $status
    	]);
    	// 写入之后要添加判断事件，判断流程是否可以结束
    	// event(new PunishRecordAddedEvent($result));
    	return $result->id;
    }
}
