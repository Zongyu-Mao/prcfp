<?php

namespace App\Home\Organization\Group;

use Illuminate\Database\Eloquent\Model;
use App\Events\Organization\Group\GroupDoc\GroupDocCreatedEvent;

class GroupDoc extends Model
{
    protected $fillable = ['gid','title','summary','content','type','status','creator_id','creator'];

    //一对一关联，获得组织信息
    public function getGroup(){
        return $this->belongsTo('App\Home\Organization\Group','gid','id');
    }

    // 获取评论
    //处理讨论区的显示
    protected function commentChild(){
        return $this-> hasMany('App\Home\Organization\Group\GroupDoc\GroupDocComment','pid','id');
    }

    public function allComments() {
        return $this->discussChild()->with('allComments');
    }

    // 写入文章
    protected function docCreate($gid,$title,$summary,$content,$creator_id,$creator) {
        $result = GroupDoc::create([
            'gid'   => $gid,
            'title' => $title,
            'summary'=> $summary,
            'content'=>$content,
            'creator_id'=>$creator_id,
            'creator' => $creator
        ]);
        event(new GroupDocCreatedEvent($result));
        return $result->id;
    }

    // 更改
    protected function modify($id,$content) {
        $result = GroupDoc::where('id',$id)->update([
            'content'=>$content
        ]);
        if($result)event(new GroupDocCreatedEvent(GroupDoc::find($id)));
        return $result;
    }
}
