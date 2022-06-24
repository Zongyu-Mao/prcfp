<?php

namespace App\Home\Organization\Group;

use Illuminate\Database\Eloquent\Model;

class GroupEmblem extends Model
{
    protected $fillable = ['gid', 'url'];

    //添加图片到数据库
    protected function emblemAdd($gid,$url){
    	$result = GroupEmblem::create([
    		'gid'	=> $gid,
    		'url'	=> $url
    	]);
    	return $result->id;
    }

    //更新数据库图片,这里的id是图片id，user_id不需要考虑啦
    protected function emblemUpdate($id,$url){
    	$result = GroupEmblem::where('id',$id)->update([
    		'url'	=> $url
    	]);
        return $result;
    }
}
