<?php

namespace App\Models\Committee\Surveillance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\Management\Surveillance\MarkEvent;

class SurveillanceMark extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $fillable = [
        'user_id','weight','tcid','sid','mark_ids','wid','status','remark'
    ];

    // 巡查标记关联巡查者
    public function author(){
        return $this->hasOne('App\Models\User','id','user_id');
    }
    // 巡查标记关联分类
    public function topClass(){
        return $this->hasOne('App\Home\Classification','tcid','id');
    }
    // 巡查标记关联主内容
    public function content(){
    	return $this->hasOne('App\Home\Encyclopedia\Entry','id','sid');    
    }
    // 关联标记处理
    public function dispose(){
        return $this->hasOne('App\Models\Committee\Surveillance\SurveillanceMarkDisposeWay','id','wid');    
    }

    // 记录
    public function records(){
        return $this->hasOne('App\Models\Committee\Surveillance\SurveillanceMarkReactRecord','id','mark_id');    
    }

    //创建
    protected function newMark($user_id,$weight,$tcid,$sid,$mark_ids,$dispose_way,$status,$remark) {
        $result = SurveillanceMark::create([
            'user_id'   => $user_id,
            'weight'   => $weight,
            'tcid'   => $tcid,
            'sid'   => $sid,
            'mark_ids'   => $mark_ids,
            'wid'   => $dispose_way,
            'status' => $status,
            'remark' => $remark
        ]);
        event(new MarkEvent($result));
        return $result->id;
    }

    //更改状态，这个一般直接在原函数改了，不再用这个调取
    protected function statusUpdate($id,$status) {
        $result = SurveillanceMark::where('id',$id)->update([
            'status'   => $status,
        ]);
        return $result;
    }
}
