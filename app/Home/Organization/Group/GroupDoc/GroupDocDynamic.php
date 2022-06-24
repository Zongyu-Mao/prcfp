<?php

namespace App\Home\Organization\Group\GroupDoc;

use Illuminate\Database\Eloquent\Model;

class GroupDocDynamic extends Model
{
	public $timestamps = false;
    protected $fillable = ['did','dTitle','behavior','objectName','objectURL','createtime'];
    // 添加用户动态事件
    protected function dynamicAdd($did,$dTitle,$behavior,$objectName,$objectURL,$createtime){
    	$result = GroupDocDynamic::create([
            'did'   	=> $did,
            'dTitle'	=> $dTitle,
            'behavior'  => $behavior,
            'objectName'=> $objectName,
            'objectURL' => $objectURL,
            'createtime'=>$createtime,
        ]);
        return $result->id;
    }
}
