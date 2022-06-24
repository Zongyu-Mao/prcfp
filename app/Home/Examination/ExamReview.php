<?php

namespace App\Home\Examination;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\ExamReview\ExamReviewCreatedEvent;

class ExamReview extends Model
{
    protected $fillable = ['exam_id','target','cid','deadline','title','content','initiate_id','initiater'];


    // 一对一关联词条,获得词条信息
    public function getExam(){
        return $this->belongsTo('App\Home\Examination\Exam','exam_id','id');
    }
    //一对一关联，获得试卷信息
    public function getContent(){
        return $this->belongsTo('App\Home\Examination\Exam','exam_id','id');
    }
    // 一对多关联评审投票记录
    public function getReviewRecord(){
        return $this->hasMany('App\Home\Examination\ExamReview\ExamReviewRecord','review_id','id');
    }

    // 一对一关联组长信息
    public function managerInfo(){
        return $this->belongsTo('App\Models\User','initiate_id','id');
    }

    //建立评审计划
    protected function reviewCreate($exam_id,$target,$cid,$deadline,$title,$content,$initiate_id,$initiate,$entryTitle){
        $result = ExamReview::create([
            'exam_id'   => $exam_id,
            'target'    => $target,
            'cid' => $cid,
            'deadline'  => $deadline,
            'title'     => $title,
            'content'   => $content,
            'initiate_id' => $initiate_id,
            'initiater'   => $initiate,
        ]);
        event(new examReviewCreatedEvent($result));
        return $result->id;
    }
}
