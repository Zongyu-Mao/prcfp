<?php

namespace App\Home\Examination\Exam;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\Exam\Focus\ExamFocusedEvent;
use App\Events\Examination\Exam\Focus\ExamFocusCanceledEvent;

class ExamFocusUser extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id','exam_id'];

    // 用户关注试卷
    protected function examFocus($user_id,$exam_id){
        $result = ExamFocusUser::create([
            'user_id'   => $user_id,
            'exam_id'    => $exam_id
        ]);
        event(new ExamFocusedEvent($result));
        return $result->id;
    }

    // 用户取消关注
    protected function examFocusCancel($user_id,$exam_id){
        $res = ExamFocusUser::where([['user_id',$user_id],['exam_id',$exam_id]])->first();
        $result = ExamFocusUser::where('id',$res->id)->delete();
        event(new ExamFocusCanceledEvent($res));
        return $result;
    }
}
