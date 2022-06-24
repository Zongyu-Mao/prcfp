<?php

namespace App\Models\Committee\Surveillance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\Management\Surveillance\TypeCreatedEvent;
use App\Events\Management\Surveillance\TypeModyfiedEvent;

class SurveillanceMarkType extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $fillable = [
        'title','sort','weight','description','creator_id','editor_id'
    ];

    //创建
    protected function newMarkType($title,$sort,$weight,$description,$creator_id,$editor_id) {
        $result = SurveillanceMarkType::create([
            'title'   => $title,
            'sort'   => $sort,
            'weight'   => $weight,
            'description'   => $description,
            'creator_id'   => $creator_id,
            'editor_id'   => $editor_id
        ]);
        event(new TypeCreatedEvent($result));
        return $result->id;
    }
    // 修改
    // handle处理
    protected function modifyMark($id,$title,$sort,$weight,$description,$editor_id) {
        $result = SurveillanceMarkType::where('id',$id)->update([
            'title'   => $title,
            'sort'   => $sort,
            'weight'   => $weight,
            'description'   => $description,
            'editor_id'   => $editor_id
        ]);
        event(new TypeModyfiedEvent(SurveillanceMarkType::find($id)));
        return $result;
    }
}
