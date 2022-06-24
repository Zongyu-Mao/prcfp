<?php

namespace App\Home\Personnel;

use Illuminate\Database\Eloquent\Model;

class JudgementInform extends Model
{
    protected $fillable = ['author_id','object_user_id','title','weight','content','url','remark','scope','ground_id','status'];

    // 关联user表
    // 一对一关联得到作者
    public function author(){
        return $this->belongsTo('App\Models\User','author_id','id');
    }

    // 一对一关联得到对象用户
    public function getTarget(){
        return $this->belongsTo('App\Models\User','object_user_id','id');
    }


    // 多对多关联得到功章
    public function getMedals(){
        return $this->belongsToMany('App\Home\Personnel\Medal','judgement_inform_medals','inform_id','medal_id');
    }

    // 返回inform的内容链接
    protected function getInformObjectUrl($id){
    	$inform = Inform::find($id);
    	
    }
    // 一对多得到处理记录
    public function records(){
        return $this->hasMany('App\Home\Personnel\Inform\InformOperateRecord','inform_id','id');
    }


    // 更新信息状态
    protected function updateStatus($id,$status){
        JudgementInform::where('id',$id)->update(['status' => $status]);
    }

    //举报信息的写入
    protected function informAdd($author_id,$object_user_id,$title,$weight,$content,$url,$remark,$scope,$ground_id,$status) {
        $result = JudgementInform::create([
            'author_id'	=> $author_id,
            'object_user_id'	=> $object_user_id,
            'title'  	=> $title,
            'weight'  	=> $weight,
            'content'	=> $content,
            'url'		=> $url,
            'remark'    => $remark,
            'scope'		=> $scope,
            'ground_id'	=> $ground_id,
            'status'	=> $status
        ]);
        // event
        return $result;
    }
}
