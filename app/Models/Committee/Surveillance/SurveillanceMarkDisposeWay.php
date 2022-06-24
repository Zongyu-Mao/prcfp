<?php

namespace App\Models\Committee\Surveillance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\Management\Surveillance\WayCreatedEvent;
use App\Events\Management\Surveillance\WayModyfiedEvent;

class SurveillanceMarkDisposeWay extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $fillable = [
        'title','sort','weight','description','creator_id','editor_id'
    ];

    //创建
    protected function newMarkDispose($title,$sort,$weight,$description,$creator_id,$editor_id) {
        $result = SurveillanceMarkDisposeWay::create([
            'title'   => $title,
            'sort'   => $sort,
            'weight'   => $weight,
            'description'   => $description,
            'creator_id'   => $creator_id,
            'editor_id'   => $editor_id
        ]);
        event(new WayCreatedEvent($result));
        return $result->id;
    }
    // 修改
    // handle处理
    protected function modifyMarkDispose($id,$title,$sort,$weight,$description,$editor_id) {
        $result = SurveillanceMarkDisposeWay::where('id',$id)->update([
            'title'   => $title,
            'sort'   => $sort,
            'weight'   => $weight,
            'description'   => $description,
            'editor_id'   => $editor_id
        ]);
        event(new WayModyfiedEvent(SurveillanceMarkDisposeWay::find($id)));
        return $result;
    }
}
