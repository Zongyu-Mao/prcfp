<?php

namespace App\Models\Encyclopedia\Ambiguity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Synonym extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'eid','sid','creator_id','createtime'
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
    protected function newSynonym($eid,$sid,$creator_id,$createtime) {
        $result = Synonym::create([
            'eid'   => $eid,
            'sid'   => $sid,
            'creator_id'   => $creator_id,
            'createtime'   => $createtime
        ]);
        // event(new VoteCreatedEvent($result));
        return $result->id;
    }

    protected function clearSynonym($eid,$sid) {
        $result = Synonym::where([['eid',$eid],['sid',$sid]])->delete();
        // event(new VoteCreatedEvent($result));
        return $result;
    }
}
