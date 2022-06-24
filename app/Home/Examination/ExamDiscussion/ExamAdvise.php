<?php

namespace App\Home\Examination\ExamDiscussion;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\ExamDiscussion\ExamAdvisementCreatedEvent;
use App\Events\Examination\ExamDiscussion\ExamAdvisementAcceptedEvent;
use App\Events\Examination\ExamDiscussion\ExamAdvisementRejectedEvent;

class ExamAdvise extends Model
{
    protected $fillable = ['exam_id','deadline','title','comment','pid','author_id','author','recipient_id','recipient','round','status'];

    //一对一关联，获得试卷信息
    public function getExam(){
        return $this->belongsTo('App\Home\Examination\Exam','exam_id','id');
    }

    //写入词条建议立场的讨论信息
     protected function adviseAdd($exam_id,$deadline,$title,$comment,$pid,$author_id,$author,$round){
    	$adviseArray = array(
    		'exam_id'	=> $exam_id,
    		'title'		=> $title,
    		'deadline'	=> $deadline,
    		'comment'	=> $comment,
    		'pid'		=> $pid,
    		'author_id'	=> $author_id,
            'author'    => $author,
    		'round'	=> $round,
    		);
    	$adivse = new ExamAdvise;
    	$result = $adivse -> fill($adviseArray) -> save();
        if($adivse->id){
            event(new ExamAdvisementCreatedEvent($adivse));
        }
    	return $result ? '1':'0';
    }

    //写入词条建议的拒绝信息
     protected function rejectAdd($exam_id,$deadline,$title,$comment,$pid,$author_id,$author,$recipient_id,$recipient,$round){
        $adviseArray = array(
            'exam_id'   => $exam_id,
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
        $adivse = new ExamAdvise;
        $result = $adivse -> fill($adviseArray) -> save();
        if($adivse->id){
            ExamAdvise::where('id',$adivse->pid)->update([
                    'recipient_id'  => $author_id,
                    'recipient'     => $author,
                    'status'        => 2,
                ]);
            event(new ExamAdvisementRejectedEvent($adivse));
        }
        return $result ? '1':'0';
    }

    // 接受建议的处理
     protected function adviseAccept($id,$recipient_id,$recipient,$status){
        $result = ExamAdvise::where('id',$id)->update([
                'recipient_id'  => $recipient_id,
                'recipient'     => $recipient,
                'status'        => $status,
            ]);
        if($result){
            event(new ExamAdvisementAcceptedEvent(ExamAdvise::find($id)));
        }
        return $result ? '1':'0';
     }

    //处理建议区的回复
    protected function adviseChild(){
        return $this-> hasMany('App\Home\Examination\ExamDiscussion\ExamAdvise','pid','id');
    }

    public function allAdvise() {
        return $this->adviseChild()->with('allAdvise');
    }
}
