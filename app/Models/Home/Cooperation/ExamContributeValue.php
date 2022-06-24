<?php

namespace App\Models\Home\Cooperation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamContributeValue extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $fillable = [
        'cooperation_id', 'user_id', 'value'
    ];

    //一对一关联用户表
    public function getUser(){
        return $this->hasOne('App\Models\User','user_id','id');
    }

    // 写入
    protected function contributeAdd($cooperation_id, $user, $value=100) {
    	$result = ExamContributeValue::create([
    		'cooperation_id' 		=> $cooperation_id,  
            'user_id' 	=> $user,
            'value'  	=> $value
        ]);
        return $result->id;
    }
    // 更新
    protected function contributeUpdate($cooperation_id, $user, $value) {
        $result = ExamContributeValue::where([['cooperation_id',$cooperation_id],['user_id',$user]])->update([
            'value'     => $value
        ]);
        return $result;
    }
    // 删除
    protected function contributeDelete($cooperation_id, $user) {
    	$result = ExamContributeValue::where([
    		'cooperation_id' 		=> $cooperation_id,  
            'user_id' 	=> $user
        ])->delete();
        return $result;
    }
}
