<?php

namespace App\Home\Encyclopedia\Recommend;

use Illuminate\Database\Eloquent\Model;
use App\Events\Encyclopedia\Recommend\EntryRecommendationUpdatedEvent;

class EntryRecommendation extends Model
{
    protected $fillable = ['cid','eid'];

    public $timestamps = true;

    // 关联词条表
    public function getEntry(){
        return $this->belongTo('App\Home\Encyclopedia\Entry','eid','id');
    }

    //写入
    protected function recommendationAdd($cid,$eid) {
        $result = EntryRecommendation::create([
            'cid'  	=> $cid,
            'eid'  	=> $eid,
        ]);
        event(new EntryRecommendationUpdatedEvent($result));
        return $result;
    }

    //修改
    protected function recommendationUpdate($id,$eid) {
        $result = EntryRecommendation::where('id',$id)->update([
            'eid'  	=> $eid,
        ]);
        event(new EntryRecommendationUpdatedEvent(EntryRecommendation::find($id)));
        return $result ? '1':'0';
    }
}
