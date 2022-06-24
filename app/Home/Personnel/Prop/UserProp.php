<?php

namespace App\Home\Personnel\Prop;

use Illuminate\Database\Eloquent\Model;
use App\Home\Personnel\Prop;

class UserProp extends Model
{
    protected $fillable = ['user_id','prop_id','amount','status'];

    public $timestamps = true;

    //多对多关联道具表
    public function getProp(){
        return $this->belongsTo('App\Home\Personnel\Prop','prop_id','id');
    }

    

    // 用户道具初始化
    protected function propInitialization($user_id) {
        // 初始化用户的所有道具，适用新注册用户和其他还没考虑到的场景
        $props = Prop::all();
        $status = 1;
        $result = 0;
        User::find($user_id)->update([
            'gold' => 1,
            'silver' => 2,
            'copper' => 5,
        ]);
        if(!UserProp::where('user_id',$user_id)->exists()){
            foreach($props as $prop){
                if($prop->sort == 1){
                    UserProp::create([
                        'user_id'   => $user_id,
                        'prop_id'   => $prop->id,
                        'amount'    => 1,
                        'status'    => $status
                    ]);
                    $result++;
                }elseif($prop->sort == 2){
                    UserProp::create([
                        'user_id'   => $user_id,
                        'prop_id'   => $prop->id,
                        'amount'    => 2,
                        'status'    => $status
                    ]);
                    $result++;
                }elseif($prop->sort == 3){
                    UserProp::create([
                        'user_id'   => $user_id,
                        'prop_id'   => $prop->id,
                        'amount'    => 3,
                        'status'    => $status
                    ]);
                    $result++;
                }else{
                    UserProp::create([
                        'user_id'   => $user_id,
                        'prop_id'   => $prop->id,
                        'amount'    => 5,
                        'status'    => $status
                    ]);
                    $result++;
                }
            }
        }
        return $result; 
    }
}
