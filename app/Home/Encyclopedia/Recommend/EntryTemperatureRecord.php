<?php

namespace App\Home\Encyclopedia\Recommend;

use Illuminate\Database\Eloquent\Model;
use App\Events\Encyclopedia\Recommend\EntryTemperatureRecordAddEvent;
use App\Home\Encyclopedia\Entry;

class EntryTemperatureRecord extends Model
{
    protected $fillable = ['eid','user_id','behavior_id','createtime'];

    public $timestamps = false;

    // 新建热度记录
    protected function recordAdd($eid,$user_id,$behavior_id,$createtime){
     	$record = EntryTemperatureRecord::create([
     		'eid'	=> $eid,
     		'user_id'	=> $user_id,
     		'behavior_id'	=> $behavior_id,
     		'createtime'	=> $createtime
     	]);
     	// 热度记录新建后触发对应事件
     	event(new EntryTemperatureRecordAddEvent($record));
     	return $record->id;
    }
}
