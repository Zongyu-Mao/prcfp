<?php

namespace App\Models\Committee\Surveillance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\Management\Surveillance\SurveillanceArticleEvent;

class SurveillanceArticleRecord extends Model
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
        return $this->hasOne('App\Home\Publication\Article','id','sid');    
    }
    //创建
    protected function newRecord($user_id,$sid,$status,$stand,$editor_id,$remark) {
        $result = SurveillanceArticleRecord::create([
            'user_id'   => $user_id,
            'sid'   => $sid,
            'status'   => $status,
            'stand'   => $stand,
            'editor_id'   => $editor_id,
            'remark'   => $remark
            
        ]);
        event(new SurveillanceArticleEvent($result));
        return $result->id;
    }
    //变更 
    protected function recordUpdate($id,$status,$stand) {
        $result = SurveillanceArticleRecord::where('id',$id)->update([
            'status'   => $status,
            'stand'   => $stand
        ]);
        event(new SurveillanceArticleEvent(SurveillanceArticleRecord::find($id)));
        return $result;
    }
}
