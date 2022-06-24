<?php

namespace App\Home;

use Illuminate\Database\Eloquent\Model;

class Encyclopedia extends Model
{
    // //定义关联的数据表
    // protected $table = 'encyclopedia';

    // public $timestamps = true;

    // protected $fillable = ['cid'];

    // //关联协作计划表
    // public function encoo(){
    // 	return $this -> hasOne('App\Home\Encoo','enc_id','id');
    // }

    // //关联分类表
    //  public function classification(){
    // 	return $this -> hasOne('App\Home\Classification','cid','id');
    // }

    // //创建词条
    // public function entryCreate($cid,$title,$etitle,$creator_id,$creator,$lasteditor,$lasteditor_id) {
    //     $result = Encyclopedia::create([
    //         'cid'   => $cid,
    //         'title' => $title,
    //         'etitle'=> $etitle,
    //         'creater_id'=>$creator_id,
    //         'creater'   =>$creator,
    //         'lasteditor'=> $lasteditor,
    //         'lasteditor_id' => $lasteditor_id,
    //     ]);
    //     return $result;
    // }

    
}
