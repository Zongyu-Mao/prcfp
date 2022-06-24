<?php

namespace App\Home\Encyclopedia\Entry;

use Illuminate\Database\Eloquent\Model;
use App\Events\Encyclopedia\Entry\EntryContentCreatedEvent;
use App\Events\Encyclopedia\Entry\EntryContentModifiedEvent;
use Laravel\Scout\Searchable;

class EntryContent extends Model
{
    use Searchable;
    protected $fillable = ['eid','sort','content','editor_id','ip','big','reason'];

    //多对多关联用户表，词条有多个版本，多个版本有多个作者
    public function users(){
        return $this -> belongsToMany('App\Models\User','content_user','content_id','user_id');
    }

    public function getEntry(){
        return $this -> belongsTo('App\Home\Encyclopedia\Entry','eid','id');
    }
    public function basic(){
        return $this -> belongsTo('App\Home\Encyclopedia\Entry','eid','id');
    }

    public function getCreator(){
        return $this -> belongsTo('App\Models\User','creator_id','id');
    }

    //新建词条内容的编辑（摘要和正文内容）
    protected function entryContentCreate($eid,$sort,$content,$editor_id,$ip,$big,$reason) {
        $result = EntryContent::create([
            'eid'       => $eid,
            'sort'   	=> $sort,
            'content'	=> $content,
            'editor_id' => $editor_id,
            'ip'        => $ip,
            'big'       => $big,
            'reason'    => $reason,
        ]);
        event(new EntryContentCreatedEvent($result));
        return $result->id;
    }

    //词条内容的修改
    protected function entryContentModify($id,$content,$editor_id,$ip,$big,$reason) {
        $result = EntryContent::where('id',$id)->update([
            'content'   => $content,
            'editor_id' => $editor_id,
            'ip'        => $ip,
            'big'       => $big,
            'reason'    => $reason,
        ]);
        event(new EntryContentModifiedEvent(EntryContent::find($id)));
        return $result?1:0;
    }
}
