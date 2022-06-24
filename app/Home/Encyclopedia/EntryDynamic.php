<?php

namespace App\Home\Encyclopedia;

use Illuminate\Database\Eloquent\Model;

class EntryDynamic extends Model
{
    public $timestamps = false;

    protected $fillable = ['eid','entryTitle','behavior','objectName','objectURL','createtime'];

    // 添加用户动态事件
    protected function dynamicAdd($eid,$entryTitle,$behavior,$objectName,$objectURL,$createtime){
    	$result = EntryDynamic::create([
            'eid'   	=> $eid,
            'entryTitle'=> $entryTitle,
            'behavior'  => $behavior,
            'objectName'=> $objectName,
            'objectURL' => $objectURL,
            'createtime'=>$createtime,
        ]);
        return $result->id;
    }
}
