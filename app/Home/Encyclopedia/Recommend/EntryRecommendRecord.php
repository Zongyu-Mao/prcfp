<?php

namespace App\Home\Encyclopedia\Recommend;

use Illuminate\Database\Eloquent\Model;

class EntryRecommendRecord extends Model
{
    protected $fillable = ['cid','eid','createtime'];

    public $timestamps = false;

    //写入推荐的记录，这是每一次上推荐表的记录
    protected function recordAdd($cid,$eid,$createtime) {
        $result = EntryRecommendRecord::create([
            'cid'   => $cid,
            'eid'  	=> $eid,
            'createtime'  	=> $createtime
        ]);
        return $result->id;
    }
}
