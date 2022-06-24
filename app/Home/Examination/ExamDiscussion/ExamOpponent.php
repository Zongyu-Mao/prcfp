<?php

namespace App\Home\Examination\ExamDiscussion;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\ExamDiscussion\ExamOpponentCreatedEvent;
use App\Events\Examination\ExamDiscussion\ExamOpponentAcceptedEvent;
use App\Events\Examination\ExamDiscussion\ExamOpponentRejectedEvent;

class ExamOpponent extends Model
{
    protected $fillable = ['exam_id','deadline','title','comment','pid','author_id','author','recipient_id','recipient','stars','round','status'];
    //输出词条讨论信息

    // 一对一关联组长信息
    public function managerInfo(){
        return $this->belongsTo('App\Models\User','author_id','id');
    }

    //一对一关联，获得试卷信息
    public function getExam(){
        return $this->belongsTo('App\Home\Examination\Exam','exam_id','id');
    }

    //写入词条反对立场的讨论信息
    protected function opponentAdd($exam_id,$deadline,$title,$comment,$pid,$author_id,$author,$round){
    	$opposeArray = array(
    		'exam_id'	=> $exam_id,
    		'title'		=> $title,
    		'deadline'	=> $deadline,
    		'comment'	=> $comment,
    		'pid'		=> $pid,
    		'author_id'	=> $author_id,
            'author'    => $author,
            'round'     => $round,
    		);
    	$opponent = new ExamOpponent;
    	$result = $opponent -> fill($opposeArray) -> save();
        if($opponent->id){
            event(new ExamOpponentCreatedEvent($opponent));
        }
    	return $result ? '1':'0';
    }

    //处理反对意见的拒绝机制
    protected function rejectAdd($exam_id,$deadline,$title,$comment,$pid,$author_id,$author,$recipient_id,$recipient,$round){
        $opposeArray = array(
            'exam_id'    => $exam_id,
            'title'     => $title,
            'deadline'  => $deadline,
            'comment'   => $comment,
            'pid'       => $pid,
            'author_id' => $author_id,
            'author'    => $author,
            'recipient_id'     => $recipient_id,
            'recipient'        => $recipient,
            'round'     => $round,
            );
        $opponent = new ExamOpponent;
        $result = $opponent -> fill($opposeArray) -> save();
        if($opponent->id){
            ExamOpponent::where('id',$pid)->update([
                    'recipient_id'  => $author_id,
                    'recipient'     => $author,
                    'status'        => 2,
                ]);
            event(new ExamOpponentRejectedEvent(ExamOpponent::find($opponent->id)));
        }
        return $result ? '1':'0';
    }

    // 处理反对意见的接收
    protected function rejectAccept($id,$recipient_id,$recipient,$status){
        $result = ExamOpponent::where('id',$id)->update([
                'recipient_id'  => $recipient_id,
                'recipient'     => $recipient,
                'status'        => 1,
            ]);
        if($result){
            event(new ExamOpponentAcceptedEvent(ExamOpponent::find($id)));
        }
        return $result ? '1':'0';
    }
    //处理反对区的回复
    protected function opposeChild(){
        return $this-> hasMany('App\Home\Examination\ExamDiscussion\ExamOpponent','pid','id');
    }

    public function allOppose() {
        return $this->opposeChild()->with('allOppose');
    }
}
