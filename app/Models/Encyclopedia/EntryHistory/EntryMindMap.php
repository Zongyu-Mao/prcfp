<?php

namespace App\Models\Encyclopedia\EntryHistory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntryMindMap extends Model
{
    use HasFactory;

    protected $fillable = [
        'pid','bid','oid','title','type','creator_id','editor_id'
    ];
    // 得到主内容
    public function basicContent(){
        return $this->hasOne('App\Home\Encyclopedia\Entry','id','oid');
    }

    // 创建者
    public function creator(){
        return $this->hasOne('App\Models\User','id','creator_id');
    }

    // 编辑者
    public function editor(){
        return $this->hasOne('App\Models\User','id','editor_id');
    }

    //得到所有的子分类
    protected function elementChild(){
        return $this-> hasMany('App\Models\Encyclopedia\EntryHistory\EntryMindMap','pid','id');
    }

    public function allElements() {
        return $this->elementChild()->with('allElements');
    }

    //创建
    protected function newMindMapRecord($pid,$oid,$bid,$title,$type,$creator_id) {
        $result = EntryMindMap::create([
            'pid'   => $pid,
            'oid'   => $oid,
            'bid'   => $bid,
            'title'   => $title,
            'type'   => $type,
            'creator_id'   => $creator_id
        ]);
        // event(new VoteCreatedEvent($result));
        return $result->id;
    }

    //修改
    protected function modifyMindMapRecord($id,$bid,$title,$type,$editor_id) {
        $result = EntryMindMap::where('id',$id)->update([
            'bid'   => $bid,
            'title'   => $title,
            'type'   => $type,
            'editor_id'   => $editor_id
        ]);
        // event(new VoteCreatedEvent($result));
        return $result;
    }
}
