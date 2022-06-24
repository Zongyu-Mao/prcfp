<?php

namespace App\Home\Encyclopedia;

use Illuminate\Database\Eloquent\Model;
use App\Events\Encyclopedia\EntryResort\EntryResortCreatedEvent;
use App\Events\Encyclopedia\EntryResort\EntryResortSupportCreatedEvent;
use App\Events\Encyclopedia\EntryResort\EntryResortSupportAcceptedEvent;
use App\Events\Encyclopedia\EntryResort\EntryResortSupportRejectedEvent;

class EntryResort extends Model
{
    protected $fillable = ['eid','cid','pid','deadline','title','content','author_id','author','status','helper_ids'];

    //一对一关联，获得词条信息
    public function getContent(){
        return $this->belongsTo('App\Home\Encyclopedia\Entry','eid','id');
    }

    // 一对一关联组长信息
    public function managerInfo(){
        return $this->belongsTo('App\Models\User','author_id','id');
    }
    //关联分类表
    public function classification(){
        return $this -> hasOne('App\Home\Classification','id','cid');
    }

    // 得到帮助内容,暂时放弃comments的关联（由于数据表设计有问题）
    // public function helpers() { 
    //     return $this->hasMany('App\Home\Encyclopedia\EntryResort','pid','id')->with('comments');
    // }
    public function helpers() { 
        return $this->hasMany('App\Home\Encyclopedia\EntryResort','pid','id');
    }

    // 得到评论内容
    public function comments() { 
        return $this->hasMany('App\Home\Encyclopedia\EntryResort\EntryResortSupportComment','resortId','id');
    }

    //写入新建的求助内容
    protected function resortAdd($eid,$cid,$pid,$deadline,$title,$content,$author,$author_id){
    	$resortAdd = EntryResort::create([
            'eid'       => $eid,
            'cid'    	=> $cid,
            'pid'       => $pid,
            'title'     => $title,
            'deadline'  => $deadline,
            'content'   => $content,
            'author'    => $author,
            'author_id' => $author_id,
        ]);
        if($resortAdd->id && $resortAdd->pid == '0'){
            event(new EntryResortCreatedEvent($resortAdd));
        }elseif($resortAdd->id && $resortAdd->pid != '0'){
            event(new EntryResortSupportCreatedEvent($resortAdd));
        }
    	return $resortAdd ? '1':'0';
    }

    //帮助内容的接受
    protected function resortSupportAccept($id,$status){
        // 更改帮助方案为采纳
        $support = EntryResort::where('id',$id)->update([
                'status' => $status,
            ]);
        if($support){
            event(new EntryResortSupportAcceptedEvent(EntryResort::find($id)));
        }
        return $support ? '1':'0';
    }

    //帮助内容的拒绝
    protected function resortSupportReject($id,$status){
        // 更改帮助方案为拒绝
        $reject = EntryResort::where('id',$id)->update([
                'status' => $status,
            ]);
        if($reject){
            event(new EntryResortSupportRejectedEvent(EntryResort::find($id)));
        }
        return $reject ? '1':'0';
    }
}
