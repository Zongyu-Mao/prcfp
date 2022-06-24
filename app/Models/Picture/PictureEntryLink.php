<?php

namespace App\Models\Picture;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\Picture\PictureEntryLinkedEvent;

class PictureEntryLink extends Model
{
    use HasFactory;
    protected $fillable = ['picture_id','eid','creator_id','createtime'];

    public $timestamps = false;

    // 新建热度记录
    protected function link($picture_id,$eid,$creator_id,$createtime){
     	$result = PictureEntryLink::create([
     		'picture_id'	=> $picture_id,
     		'eid'	=> $eid,
     		'creator_id'	=> $creator_id,
     		'createtime'	=> $createtime,
     	]);
     	// 热度记录新建后触发对应事件
     	event(new PictureEntryLinkedEvent($result));
     	return $result->id;
    }
}
