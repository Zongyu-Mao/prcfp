<?php

namespace App\Models\Picture;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Listeners\Picture\PictureGiveLikeEvent;

class PictureGiveLikeRecord extends Model
{
    use HasFactory;

    // public $timestamps = false;

    protected $fillable = [
        'picture_id','user_id','stand'
    ];
    // 一对一关联得到作者
    public function author(){
        return $this->belongsTo('App\Models\User','user_id','id');
    }

    //创建
    protected function newRecord($picture_id,$user_id,$stand) {
        $result = PictureGiveLikeRecord::create([
            'picture_id'   => $picture_id,
            'user_id'   => $user_id,
            'stand'   => $stand,
        ]);
        event(new PictureGiveLikeEvent($result));
        return $result->id;
    }

    //更改
    protected function standChange($id,$stand) {
        $result = PictureGiveLikeRecord::where('id',$id)->update([
            'stand'   => $stand
        ]);
        // event(new RoleAppliedEvent($result));
        return $result;
    }
}
