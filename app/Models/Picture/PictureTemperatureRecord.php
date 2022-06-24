<?php

namespace App\Models\Picture;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\Picture\PictureTemperatureRecordAddedEvent;

class PictureTemperatureRecord extends Model
{
    use HasFactory;
    protected $fillable = ['picture_id','user_id','behavior_id','createtime'];

    public $timestamps = false;

    // 新建热度记录
    protected function recordAdd($picture_id,$user_id,$behavior_id,$createtime){
     	$result = PictureTemperatureRecord::create([
     		'picture_id'	=> $picture_id,
     		'user_id'	=> $user_id,
     		'behavior_id'	=> $behavior_id,
     		'createtime'	=> $createtime,
     	]);
     	// 热度记录新建后触发对应事件
     	event(new PictureTemperatureRecordAddedEvent($result));
     	return $result->id;
    }
}
