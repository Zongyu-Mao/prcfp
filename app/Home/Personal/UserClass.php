<?php

namespace App\Home\Personal;

use Illuminate\Database\Eloquent\Model;

class UserClass extends Model
{
    protected $fillable = ['user_id', 'class_id'];

    public $timestamps = false;

    //添加兴趣专业到数据库
    protected function classAdd($user_id,$class_id){
    	$result = UserClass::create([
    		'user_id'	=> $user_id,
    		'class_id'		=>$class_id
    	]);
    	return $result->id;
    }

    //删除兴趣专业到数据库
    protected function classDelete($user_id,$class_id){
    	$result = UserClass::where([['user_id',$user_id],['class_id',$class_id]])->delete();
    	return $result?1:0;
    }
}
