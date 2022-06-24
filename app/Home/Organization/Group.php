<?php

namespace App\Home\Organization;

use Illuminate\Database\Eloquent\Model;
use App\Events\Organization\GroupCreatedEvent;
use App\Events\Organization\Group\GroupIntroModifiedEvent;
use App\Events\Organization\ManagerUpdatedEvent;
use App\Home\Classification;
use Laravel\Scout\Searchable;

class Group extends Model
{
    use Searchable;
    //组织模型
    // public $timestamps = true;

    protected $fillable = ['cid','emblem','title','introduction','ifSeeking','level','status','manager','manage_id','creator','creator_id'];

    //关联分类表
    public function classification(){
    	return $this -> hasOne('App\Home\Classification','id','cid');
    }

    //获取组织徽章
    public function groupEmblem(){
        return $this -> hasOne('App\Home\Organization\Group\GroupEmblem','id','emblem');
    }
    public function avatar(){
        return $this -> hasOne('App\Home\Organization\Group\GroupEmblem','id','emblem');
    }

    //获取组织文档
    public function groupDocs(){
        return $this -> hasMany('App\Home\Organization\Group\GroupDoc','gid','id');
    }

    // 返回组织用户
    protected function groupUsers(){
        return $this->hasMany('App\Home\Organization\Group\GroupUser','gid','id');
    }

    // 获得关注用户
    public function groupFocus(){
        return $this->belongsToMany('App\Models\User','group_focus_users','gid','user_id');
    }

    // 一对一关联组长信息
    public function managerInfo(){
        return $this->belongsTo('App\Models\User','manage_id','id');
    }

    //多对多关联成员表
    public function members(){
        return $this->belongsToMany('App\Models\User','group_users','gid','user_id');
    }
    //多对多关联成员表
    public function crews(){
        return $this->belongsToMany('App\Models\User','group_users','gid','user_id');
    }

    //创建组织
    protected function groupCreate($cid,$title,$introduction,$ifSeeking,$manager,$manage_id,$creator,$creator_id) {
        $result = Group::create([
            'cid'   => $cid,
            'title' => $title,
            'introduction'	=> $introduction,
            'ifSeeking'		=> $ifSeeking,
            'manager'	=> $manager,
            'manage_id'	=>$manage_id,
            'creator'	=>$creator,
            'creator_id'=>$creator_id
        ]);
        event(new GroupCreatedEvent($result));
        return $result->id;
    }

    //更改摘要
    protected function introModify($oId,$introduction){
    	$result = Group::where('id',$oId)->update([
            'introduction'   => $introduction,
        ]);
        $group = Group::find($oId);
        event(new GroupIntroModifiedEvent($group));
        return $result?'1':'0';
    }

    //更换管理员
    protected function manageUpdate($id,$manage_id) {
        $result = Group::where('id',$id)->update([
            'manage_id'=>$manage_id
        ]);
        event(new ManagerUpdatedEvent(Group::find($id)));
        return $result;
    }
    //更换封面
    protected function avatarUpdate($id,$avatar_id) {
        $result = Group::where('id',$id)->update([
            'emblem'=>$avatar_id
        ]);
        return $result;
    }
}
