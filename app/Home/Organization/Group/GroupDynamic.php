<?php

namespace App\Home\Organization\Group;

use Illuminate\Database\Eloquent\Model;

class GroupDynamic extends Model
{
    //著作动态的新建
    public $timestamps = false;

    protected $fillable = ['gid','gTitle','behavior','objectName','objectURL','createtime'];

    // 添加用户动态事件
    protected function dynamicAdd($gid,$gTitle,$behavior,$objectName,$objectURL,$createtime){
    	$result = GroupDynamic::create([
            'gid'   	=> $gid,
            'gTitle'	=> $gTitle,
            'behavior'  => $behavior,
            'objectName'=> $objectName,
            'objectURL' => $objectURL,
            'createtime'=>$createtime,
        ]);
        return $result->id;
    }
}
