<?php

namespace App\Home\Examination;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\ExamResort\ExamResortCreatedEvent;
use App\Events\Examination\ExamResort\ExamResortSupportCreatedEvent;
use App\Events\Examination\ExamResort\ExamResortSupportAcceptedEvent;
use App\Events\Examination\ExamResort\ExamResortSupportRejectedEvent;

class ExamResort extends Model
{
    protected $fillable = ['exam_id','cid','pid','deadline','title','content','author_id','author','status'];

    //一对一关联，获得试卷信息
    public function getContent(){
        return $this->belongsTo('App\Home\Examination\Exam','exam_id','id');
    }

    // 一对一关联组长信息
    public function managerInfo(){
        return $this->belongsTo('App\Models\User','author_id','id');
    }
    // 得到帮助内容
    public function helpers() { 
        return $this->hasMany('App\Home\Examination\ExamResort','pid','id')->with('comments');
    }
    // 得到评论内容
    public function comments() { 
        return $this->hasMany('App\Home\Examination\ExamResort\ExamResortSupportComment','resortId','id');
    }

    //写入新建的求助内容
    protected function resortAdd($exam_id,$cid,$pid,$deadline,$title,$content,$author,$author_id){
    	$resortAdd = ExamResort::create([
            'exam_id'   => $exam_id,
            'cid'       => $cid,
            'pid'       => $pid,
            'title'     => $title,
            'deadline'  => $deadline,
            'content'   => $content,
            'author'    => $author,
            'author_id' => $author_id,
        ]);
        if($resortAdd->id && $resortAdd->pid == 0){
            event(new ExamResortCreatedEvent($resortAdd));
        }elseif($resortAdd->id && $resortAdd->pid != 0){
            event(new ExamResortSupportCreatedEvent($resortAdd));
        }
    	return $resortAdd ? '1':'0';
    }

    //帮助内容的接受
    protected function resortSupportAccept($id,$status){
        // 更改帮助方案为采纳
        $support = ExamResort::where('id',$id)->update([
                'status' => '1',
            ]);
        if($support){
            event(new ExamResortSupportAcceptedEvent(ExamResort::find($id)));
        }
        return $support ? '1':'0';
    }

    //帮助内容的拒绝
    protected function resortSupportReject($id,$status){
        // 更改帮助方案为采纳
        $reject = ExamResort::where('id',$id)->update([
                'status' => '2',
            ]);
        if($reject){
            event(new ExamResortSupportRejectedEvent(ExamResort::find($id)));
        }
        return $reject ? '1':'0';
    }
}
