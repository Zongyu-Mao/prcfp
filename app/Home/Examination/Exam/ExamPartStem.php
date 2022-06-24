<?php

namespace App\Home\Examination\Exam;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\PartStem\ExamPartStemCreatedEvent;
use App\Events\Examination\PartStem\ExamPartStemModifiedEvent;

class ExamPartStem extends Model
{
    protected $fillable = ['exam_id','sort','qid','questions','content','lock','creator_id','editor_id','ip','big','reason'];

    // 获得该材料下的所有题目
    public function getPartQuestions(){
    	return $this -> hasMany('App\Home\Examination\Exam\ExamQuestion','id','partStem');
    }
    // 新建材料内容
    protected function examPartStemCreate($exam_id,$sort,$qid,$questions,$content,$lock,$creator_id,$editor_id,$ip,$big,$reason) {
        $result = ExamPartStem::create([
            'exam_id'   => $exam_id,
            'sort'   => $sort,
            'qid'   => $qid,
            'questions'     => $questions,
            'content'      => $content,
            'lock'   	=> $lock,
            'creator_id' => $creator_id,
            'editor_id' => $editor_id,
            'ip'        => $ip,
            'big'       => $big,
            'reason'    => $reason,
        ]);
        // 我们的逻辑是先写入材料，再写入question，因此
        // 这里只触发stem的created事件，与question其实毫无关系，这时仅仅是stem与qid绑定了而已，id等于qid的question现在还没有
        event(new ExamPartStemCreatedEvent($result));
        return $result->id;
    }

    // 编辑材料内容
    protected function examPartStemModify($id,$content,$lock,$editor_id,$ip,$big,$reason) {
        $result = ExamPartStem::where('id',$id)->update([
            'content'   => $content,
            'lock'      => $lock,
            'editor_id' => $editor_id,
            'ip'        => $ip,
            'big'       => $big,
            'reason'    => $reason,
        ]);
        event(new ExamPartStemModifiedEvent(ExamPartStem::find($id)));
        return $result?'1':'0';
    }
}
