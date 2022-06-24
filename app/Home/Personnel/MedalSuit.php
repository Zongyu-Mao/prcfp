<?php

namespace App\Home\Personnel;

use Illuminate\Database\Eloquent\Model;
use App\Events\Personnel\MedalSuit\MedalSuitCreatedEvent;

class MedalSuit extends Model
{
    protected $fillable = ['title','type','amount','description','creator_id','status'];
    // public $timestamps = false;

    // 关联user获得作者
    public function getCreator(){
        return $this->belongsTo('App\Models\User','creator_id','id');
    }

    // 功章的更改应该设置事件
    // event

    // 一对多关联功章表得到所有套件
    public function getMedals(){
        return $this->hasMany('App\Home\Personnel\Medal','suit_id','id')->orderBy('sort','asc');
    }

	//写入
    protected function medalSuitAdd($title,$type,$amount,$description,$creator_id) {
        $result = MedalSuit::create([
            'title'	=> $title,
            'type'	=> $type,
            'amount'	=> $amount,
            'description'	=>$description,
            'creator_id'	=>$creator_id,
        ]);
        event(new MedalSuitCreatedEvent($result));
        return $result->id;
    }

    //状态修改
    protected function statusUpdate($id,$status) {
        $result = MedalSuit::where('id',$id)->update([
            'status'  => $status
        ]);
        return $result ;
    }

    //用户角色的属性修改
    protected function medalSuitModify($id,$rolename,$creditslower) {
        $result = MedalSuit::where('id',$id)->update([
            'role'   	=> $rolename,
            'creditslower'	=> $creditslower,
        ]);
        return $result;
    }

    //删除
    protected function medalSuitDelete($id) {
        $result = MedalSuit::where('id',$id)->delete();
        return $result;
    }
}
