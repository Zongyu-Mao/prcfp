<?php

namespace App\Models\Committee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Committee extends Model
{
    use HasFactory, Searchable;
    public $timestamps = true;

    protected $fillable = [
        'title','tcid','scid','thcid','cid','hierarchy', 'emblem', 'introduction','manage_id','creator_id'
    ];

    // 巡查标记关联巡查者
    public function manager(){
        return $this->hasOne('App\Models\User','id','manage_id');
    }
    public function creator(){
        return $this->hasOne('App\Models\User','id','creator_id');
    }
    // 巡查标记关联顶级分类
    public function topClass(){
        return $this->hasOne('App\Home\Classification','tcid','id');
    }
    // 巡查标记关联顶级分类
    public function class(){
        return $this->hasOne('App\Home\Classification','cid','id');
    }
    //获取组织文档
    public function documents(){
        return $this -> hasMany('App\Models\Committee\CommitteeDocument','tcid','id');
    }
    //获取成员
    public function members(){
        return $this -> hasMany('App\Models\User','committee_id','id');
    }

    //创建
    protected function newCommittee($title,$tcid,$scid,$thcid,$cid,$hierarchy,$emblem,$introduction,$creator_id) {
        $result = Committee::create([
            'title'   => $title,
            'tcid'   => $tcid,
            'scid'   => $scid,
            'thcid'   => $thcid,
            'cid'   => $cid,
            'hierarchy'   => $hierarchy,
            'emblem'   => $emblem,
            'introduction' => $introduction,
            'creator_id' => $creator_id
        ]);
        // event(new VoteCreatedEvent($result));
        return $result->id;
    }
    // handle处理
    protected function committeeModify($id,$emblem,$introduction) {
        $result = Committee::where('id',$id)->update([
            'emblem'   => $emblem,
            'introduction'   => $introduction
        ]);
        // event(new VoteCreatedEvent($result));
        return $result;
    }

    // 更换管理
    protected function managerUpdate($id,$manage_id) {
        $result = Committee::where('id',$id)->update([
            'manage_id'   => $manage_id,
        ]);
        // event(new VoteCreatedEvent($result));
        return $result;
    }
}
