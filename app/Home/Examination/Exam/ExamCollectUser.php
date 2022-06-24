<?php

namespace App\Home\Examination\Exam;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\Exam\Focus\ExamCollectedEvent;
use App\Events\Examination\Exam\Focus\ExamCollectCanceledEvent;

class ExamCollectUser extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id','exam_id'];

    protected function examCollect($user_id,$exam_id){
        $result = ExamCollectUser::create([
            'user_id'   => $user_id,
            'exam_id'    => $exam_id
        ]);
        event(new ExamCollectedEvent($result));
        return $result->id;
    }

    // 用户取消收藏
    protected function examCollectCancel($user_id,$exam_id){
        $res = ExamCollectUser::where([['user_id',$user_id],['exam_id',$exam_id]])->first();
        $result = ExamCollectUser::where('id',$res->id)->delete();
        event(new ExamCollectCanceledEvent($res));
        return $result;
    }
}
