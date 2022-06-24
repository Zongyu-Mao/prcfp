<?php

namespace App\Home\Publication\Recommend;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\Recommend\ArticleTemperatureRecordAddedEvent;

class ArticleTemperatureRecord extends Model
{
    protected $fillable = ['aid','user_id','behavior_id','createtime'];

    public $timestamps = false;

    // 新建热度记录
    protected function recordAdd($aid,$user_id,$behavior_id,$createtime){
     	$result = ArticleTemperatureRecord::create([
     		'aid'	=> $aid,
     		'user_id'	=> $user_id,
     		'behavior_id'	=> $behavior_id,
     		'createtime'	=> $createtime,
     	]);
     	// 热度记录新建后触发对应事件
     	event(new ArticleTemperatureRecordAddedEvent($result));
     	return $result->id;
    }
}
