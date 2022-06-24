<?php

namespace App\Models\Committee\Surveillance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\Management\Surveillance\SurveillanceEvent;

class SurveillanceRecord extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $fillable = [
        'user_id','sid','status','stand','editor_id','remark'
    ];
    // 一对一关联得到作者
    public function author(){
        return $this->belongsTo('App\Models\User','user_id','id');
    }
    // 巡查标记关联主内容
    public function content(){
        return $this->hasOne('App\Home\Encyclopedia\Entry','id','sid');    
    }
    //创建
    protected function newRecord($user_id,$sid,$status,$stand,$editor_id,$remark) {
        $result = SurveillanceRecord::create([
            'user_id'   => $user_id,
            'sid'   => $sid,
            'status'   => $status,
            'stand'   => $stand,
            'editor_id'   => $editor_id,
            'remark'   => $remark
        ]);
        event(new SurveillanceEvent($result));
        return $result->id;
    }
    //变更 
    protected function recordUpdate($id,$status,$stand) {
        $result = SurveillanceRecord::where('id',$id)->update([
            'status'   => $status,
            'stand'   => $stand
        ]);
        event(new SurveillanceEvent(SurveillanceRecord::find($id)));
        return $result;
    }
}
