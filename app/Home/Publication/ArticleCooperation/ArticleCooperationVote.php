<?php

namespace App\Home\Publication\ArticleCooperation;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\ArticleCooperation\ArticleCooperationVoteCreatedEvent;

class ArticleCooperationVote extends Model
{
    protected $fillable = ['cooperation_id','aid','type','deadline','initiate_id','initiate','title','content','status','remark'];

    // 一对一关联发起者信息
    public function getCreator(){
        return $this->hasOne('App\Models\User','id','initiate_id');
    }

    // 一对多关联投票记录
    public function getVoteRecord(){
        return $this->hasMany('App\Home\Publication\ArticleCooperation\ArticleCooperationVoteRecord','vote_id','id');
    }



    // 处理协作计划的投票创建,投票仅存在type的不同，所以可以用一个函数全部处理
    protected function cooperationVote($cooperation_id,$aid,$type,$deadline,$initiate_id,$initiate,$title,$content){
        $voteArr = array(
            'cooperation_id'  => $cooperation_id,
            'aid'    => $aid,
            'type'      => $type,
            'deadline'  => $deadline,
            'initiate_id' => $initiate_id,
            'initiate' => $initiate,
            'title'     => $title,
            'content'   => $content,
            );
        $ArticleCooperationVote = new ArticleCooperationVote;
        $result = $ArticleCooperationVote -> fill($voteArr) -> save();
        if($ArticleCooperationVote->id){
            event(new ArticleCooperationVoteCreatedEvent($ArticleCooperationVote));
        }
        return $ArticleCooperationVote->id ? '1':'0';
    }
}
