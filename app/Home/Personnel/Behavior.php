<?php

namespace App\Home\Personnel;

use Illuminate\Database\Eloquent\Model;
use App\Events\Personnel\Behavior\BehaviorCreatedEvent;

class Behavior extends Model
{
    protected $fillable = ['sort','name','score','introduction'];

    public $timestamps = true;

    //写入
    protected function behaviorAdd($name,$score,$introduction) {
        $result = Behavior::create([
            'name'  	=> $name,
            'score'		=>$score,
            'introduction'  =>$introduction
        ]);
        // 触发行为新建的时间
        event(new BehaviorCreatedEvent($result));
        return $result->id;
    }

    //修改
    protected function behaviorModify($id,$name,$score,$introduction) {
        $result = Behavior::where('id',$id)->update([
            'name'  => $name,
            'score'	=> $score,
            'introduction'      =>$introduction
        ]);
        event(new BehaviorCreatedEvent(Behavior::find($id)));
        return $result;
    }

    //删除
    protected function behaviorDelete($id) {
        $result = Behavior::where('id',$id)->delete();
        return $result;
    }
}
