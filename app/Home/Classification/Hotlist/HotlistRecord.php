<?php

namespace App\Home\Classification\Hotlist;

use Illuminate\Database\Eloquent\Model;

class HotlistRecord extends Model
{
    protected $fillable = ['hotlist_id','user_id','createtime'];

    public $timestamps = false;

    // 添加记录
    protected function hotlistRecordAdd($hotlist_id,$user_id,$createtime){
    	$result = HotlistRecord::create([
            'hotlist_id'   	=> $hotlist_id,
            'user_id'		=> $user_id,
            'createtime'	=> $createtime,
        ]);
        return $result->id;
    }
}
