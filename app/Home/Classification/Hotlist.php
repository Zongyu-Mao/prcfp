<?php

namespace App\Home\Classification;

use Illuminate\Database\Eloquent\Model;

class Hotlist extends Model
{
    protected $fillable = ['cid','list_id','type','editor_id'];
    // public $timestamps = false;

    // 添加推荐内容
    protected function hotlistAdd($cid,$list_id,$type,$editor_id){
    	$result = Hotlist::create([
            'cid'   	=> $cid,
            'list_id'	=> $list_id,
            'type'		=> $type,
            'editor_id'	=> $editor_id,
        ]);
        // 这里要触发事件，写入记录，或者控制器直接写入了
        return $result->id;
    }

    // 更新推荐内容,只能更改list和editor，cid和type是不能更改的
    protected function hotlistUpdate($hid,$list_id,$editor_id){
    	$result = Hotlist::where('id',$hid)->update([
            'list_id'	=> $list_id,
            'editor_id'	=> $editor_id,
        ]);
        return $result->id;
    }
}
