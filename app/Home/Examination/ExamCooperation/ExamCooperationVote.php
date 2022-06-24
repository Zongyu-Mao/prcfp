<?php

namespace App\Home\Examination\ExamCooperation;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\ExamCooperation\Vote\VoteCreatedEvent;

class ExamCooperationVote extends Model
{
    protected $fillable = ['cooperation_id','exam_id','type','deadline','initiate_id','initiate','title','content','status','remark'];

    // 一对一关联发起者信息
    public function getCreator(){
        return $this->hasOne('App\User','id','initiate_id');
    }

    // 一对多关联投票记录
    public function getVoteRecord(){
        return $this->hasMany('App\Home\Examination\ExamCooperation\ExamCooperationVoteRecord','vote_id','id');
    }



    // 处理协作计划的投票创建,投票仅存在type的不同，所以可以用一个函数全部处理
    protected function cooperationVote($cooperation_id,$exam_id,$type,$deadline,$initiate_id,$initiate,$title,$content){
        $voteArr = array(
            'cooperation_id'  => $cooperation_id,
            'exam_id'    => $exam_id,
            'type'      => $type,
            'deadline'  => $deadline,
            'initiate_id' => $initiate_id,
            'initiate' => $initiate,
            'title'     => $title,
            'content'   => $content,
            );
        $ExamCooperationVote = new ExamCooperationVote;
        $result = $ExamCooperationVote -> fill($voteArr) -> save();
        if($ExamCooperationVote->id){
            event(new VoteCreatedEvent($ExamCooperationVote));
        }
        return $ExamCooperationVote->id ? '1':'0';
    }
}
