<?php

namespace App\Home\Examination\Exam\Question;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\Exam\QuestionMethod\ExamQuestionOptionModifiedEvent;

class ExamQuestionOption extends Model
{
    protected $fillable = ['qid','option','sort','creator_id','editor_id','ip','big','reason'];

    // 写入选项,是一个一个写入的
    protected function questionOptionCreate($qid,$option,$sort,$creator_id,$editor_id,$ip,$big,$reason) {
        $result = ExamQuestionOption::create([
            'qid'   	=> $qid,
            'option'   	=> $option,
            'sort'     	=> $sort,
            'creator_id' => $creator_id,
            'editor_id' => $editor_id,
            'ip'        => $ip,
            'big'       => $big,
            'reason'    => $reason,
        ]);
        // 写入选项暂时不考虑触发事件，因为这是在题目创建时一起的
        return $result->id;
    }

    // 选项的编辑修改
    protected function questionOptionModify($id,$option,$editor_id,$ip,$big,$reason) {
        $result = ExamQuestionOption::where('id',$id)->update([
            'option'    => $option,
            'editor_id' => $editor_id,
            'ip'        => $ip,
            'big'       => $big,
            'reason'    => $reason,
        ]);
        // 更改选项考虑事件
        event(new ExamQuestionOptionModifiedEvent(ExamQuestionOption::find($id)));
        return $result;
    }
}
