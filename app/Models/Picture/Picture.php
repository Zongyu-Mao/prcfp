<?php

namespace App\Models\Picture;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Picture extends Model
{
    use HasFactory, Searchable;
    // public $timestamps = false;

    protected $fillable = [
        'showtime','cid','title','introduction','url','status','ups','downs','creator_id','creator'
    ];

    // 巡查标记关联顶级分类
    public function class(){
        return $this->hasOne('App\Home\Classification','id','cid');
    }
    // 得到entry的links,多对多
    public function links(){
        return $this->belongsToMany('App\Home\Encyclopedia\Entry','picture_entry_links','picture_id','eid');
    }

    //创建
    protected function newPicture($showtime,$cid,$title,$introduction,$url,$creator_id,$creator) {
        $result = Picture::create([
            'showtime'   => $showtime,
            'cid'   => $cid,
            'title'   => $title,
            'introduction'   => $introduction,
            'url'   => $url,
            'creator_id'   => $creator_id,
            'creator'   => $creator
        ]);
        // event(new VoteCreatedEvent($result));
        return $result->id;
    }

    // 更新title和简介
    protected function pictureModify($id,$mode,$title,$introduction,$url) {
        $result = false;
        switch($mode) {
            case 1:
            $result = Picture::where('id',$id)->update([
                'title'   => $title,
                'introduction'   => $introduction,
            ]);
            break;
            case 3:
            $result = Picture::where('id',$id)->update([
                'title'   => $title,
                'eid'   => 0
            ]);
            break;
            case 4:
            $result = Picture::where('id',$id)->update([
                'introduction'   => $introduction,
            ]);
            break;
            case 5:
            $result = Picture::where('id',$id)->update([
                'url'   => $url,
            ]);
            break;
            default:
            break;
        }
        // event(new VoteCreatedEvent($result));
        return $result;
    }

    protected function pictureEntryLink($id,$eid) {
        $result = Picture::where('id',$id)->update([
            'eid'   => $eid
        ]);
        return $result;
    }
}
