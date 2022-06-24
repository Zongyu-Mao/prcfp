<?php

namespace App\Home\Personnel\JudgementInform;

use Illuminate\Database\Eloquent\Model;
use App\Events\Personnel\Inform\InformOperate\JudgementInformOperateRecordAddedEvent;

class JudgementInformOperateRecord extends Model
{
    protected $fillable = ['inform_id','operator_id','standpoint','createtime'];
    public $timestamps = false;

    // 关联取得操作用户信息
    public function getOperator(){
    	return $this->belongsTo('App\Models\User','operator_id','id');
    }

    // 新建记录
    protected function informOperateRecordAdd($inform_id,$operator_id,$standpoint,$createtime){
    	$result = JudgementInformOperateRecord::create([
    		'inform_id'	=> $inform_id,
			'operator_id'	=> $operator_id,
			'standpoint'	=> $standpoint,
			'createtime'	=> $createtime
    	]);
    	// 写入之后要添加判断事件，判断流程是否可以结束
    	event(new JudgementInformOperateRecordAddedEvent($result));
    	return $result->id;
    }
}
