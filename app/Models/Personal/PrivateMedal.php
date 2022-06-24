<?php

namespace App\Models\Personal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\Personal\PrivateMedal\PrivateMedalGivenEvent;

class PrivateMedal extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $fillable = [
        'title','url','description','creator_id','owner_id','status'
    ];

    // 关联user获得作者
    public function creator(){
        return $this->belongsTo('App\Models\User','creator_id','id');
    }
    public function owner(){
        return $this->belongsTo('App\Models\User','owner_id','id');
    }
    //功章的写入
    protected function privateMedalAdd($title,$url,$description,$creator_id) {
        $result = PrivateMedal::create([
            'title'     =>$title,
            'url'		=>$url,
            'description'	=>$description,
            'creator_id'	=>$creator_id
        ]);
        // event(new MedalCreatedEvent($result));
        return $result->id;
    }

    //功章的属性修改
    protected function medalGiving($id,$owner_id,$status) {
        $result = PrivateMedal::where('id',$id)->update([
            'owner_id' => $owner_id,
            'status' => $status
        ]);
        event(new PrivateMedalGivenEvent(PrivateMedal::find($id)));
        return $result ? '1':'0';
    }
}
