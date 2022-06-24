<?php

namespace App\Home\Publication\Article;

use Illuminate\Database\Eloquent\Model;

class ArticleDynamic extends Model
{
    //著作动态的新建
    public $timestamps = false;

    protected $fillable = ['aid','articleTitle','behavior','objectName','objectURL','createtime'];

    // 添加用户动态事件
    protected function dynamicAdd($aid,$articleTitle,$behavior,$objectName,$objectURL,$createtime){
    	$result = ArticleDynamic::create([
            'aid'   	=> $aid,
            'articleTitle'=> $articleTitle,
            'behavior'  => $behavior,
            'objectName'=> $objectName,
            'objectURL' => $objectURL,
            'createtime'=>$createtime,
        ]);
        return $result->id;
    }
}
