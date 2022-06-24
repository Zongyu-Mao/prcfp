<?php

namespace App\Models\Committee\Surveillance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\Management\Surveillance\WarningArticleEvent;

class SurveillanceArticleWarning extends Model
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
        return $this->hasOne('App\Home\Publication\Article','id','sid');    
    }
    //创建
    protected function newWarning($user_id,$sid,$warning,$status,$createtime) {
        $result = SurveillanceArticleWarning::create([
            'user_id'   => $user_id,
            'sid'   => $sid,
            'warning'   => $warning,
            'status'   => $status,
            'createtime'   => $createtime
        ]);
        event(new WarningArticleEvent($result));
        return $result->id;
    }
    //更新
    protected function warnUpdate($id,$status) {
        $result = SurveillanceArticleWarning::where('id',$id)->update([
            'status'   => $status
        ]);
        event(new WarningArticleEvent(SurveillanceArticleWarning::find($id)));
        return $result;
    }
}
