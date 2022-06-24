<?php

namespace App\Models\Committee\Surveillance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\Management\Surveillance\WarningEvent;

class SurveillanceWarning extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'user_id','sid','warning','status','createtime'
    ];
    // 巡查标记关联巡查者
    public function author(){
        return $this->hasOne('App\Models\User','id','user_id');
    }
    // 巡查标记关联主内容
    public function content(){
        return $this->hasOne('App\Home\Encyclopedia\Entry','id','sid');    
    }
    //创建
    protected function newWarning($user_id,$sid,$warning,$status,$createtime) {
        $result = SurveillanceWarning::create([
            'user_id'   => $user_id,
            'sid'   => $sid,
            'warning'   => $warning,
            'status'   => $status,
            'createtime'   => $createtime
        ]);
        event(new WarningEvent($result));
        return $result->id;
    }
    //更新
    protected function warnUpdate($id,$status) {
        $result = SurveillanceWarning::where('id',$id)->update([
            'status'   => $status
        ]);
        event(new WarningEvent(SurveillanceWarning::find($id)));
        return $result;
    }
}
