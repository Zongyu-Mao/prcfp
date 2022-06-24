<?php

namespace App\Home\Personal;

use Illuminate\Database\Eloquent\Model;

class UserPicture extends Model
{
    protected $fillable = ['user_id', 'url'];

    //添加图片到数据库
    protected function avatarAdd($user_id,$url){
    	$result = UserPicture::create([
    		'user_id'	=> $user_id,
    		'url'		=>$url
    	]);
    	return $result->id;
    }

    //更新数据库图片,这里的id是图片id，user_id不需要考虑啦
    protected function avatarUpdate($id,$url){
    	$result = UserPicture::where('id',$id)->update([
    		'url'		=>$url
    	]);
    }
}
