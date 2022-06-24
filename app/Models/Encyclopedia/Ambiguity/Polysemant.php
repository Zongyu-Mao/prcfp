<?php

namespace App\Models\Encyclopedia\Ambiguity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Polysemant extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'eid','poly_id','creator_id','createtime'
    ];

    // 巡查标记关联巡查者
    public function entry(){
        return $this->hasOne('App\Home\Encyclopedia\Entry','eid','id');
    }
    // 巡查标记关联顶级分类
    public function creator(){
        return $this->hasOne('App\Models\User','creator_id','id');
    }

    //创建
    protected function newPolysemant($eid,$poly_id,$creator_id,$createtime) {
        $result = Polysemant::create([
            'eid'   => $eid,
            'poly_id'   => $poly_id,
            'creator_id'   => $creator_id,
            'createtime'   => $createtime
        ]);
        // event(new VoteCreatedEvent($result));
        return $result->id;
    }

    //消除
    protected function clearPolysemant($eid,$poly_id) {
        if(Polysemant::where([['eid',$eid],['poly_id',$poly_id]])->exists())$result = Polysemant::where([['eid',$eid],['poly_id',$poly_id]])->delete();
        if(Polysemant::where([['eid',$poly_id],['poly_id',$eid]])->exists())$result = Polysemant::where([['eid',$poly_id],['poly_id',$eid]])->delete();
        // event(new VoteCreatedEvent($result));
        return $result;
    }

}
