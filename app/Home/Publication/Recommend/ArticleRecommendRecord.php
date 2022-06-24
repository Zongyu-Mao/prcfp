<?php

namespace App\Home\Publication\Recommend;

use Illuminate\Database\Eloquent\Model;


class ArticleRecommendRecord extends Model
{
    protected $fillable = ['cid','aid','createtime'];

    public $timestamps = false;

    //写入推荐的记录，这是每一次上推荐表的记录
    protected function recordAdd($cid,$aid,$createtime) {
        $result = ArticleRecommendRecord::create([
            'cid'   => $cid,
            'aid'  	=> $aid,
            'createtime'  	=> $createtime
        ]);
        return $result->id;
    }
}
