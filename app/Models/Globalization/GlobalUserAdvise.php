<?php

namespace App\Models\Globalization;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalUserAdvise extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','title','content','scope','createtime','status'
    ];

    public $timestamps = false;
    // 作者
    public function creator(){
        return $this->hasOne('App\Models\User','id','user_id');
    }
    // 回复
    public function comments(){
        return $this->hasMany('App\Models\Globalization\GlobalUserAdvise\GlobalUserAdviseComment','advise_id','id');
    }

    //创建，下面两个方法目前均不涉及事件
    protected function newAdvise($user_id,$title,$content,$scope,$createtime) {
        $result = GlobalUserAdvise::create([
            'user_id'   => $user_id,
            'scope'   => $scope,
            'title'   => $title,
            'content'   => $content,
            'createtime'   => $createtime
        ]);
        return $result;
    }

}
