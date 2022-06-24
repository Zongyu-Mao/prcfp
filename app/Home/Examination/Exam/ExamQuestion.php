<?php

namespace App\Home\Examination\Exam;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\Exam\ExamQuestionCreatedEvent;
use App\Events\Examination\Exam\QuestionMethod\ExamQuestionStemModifiedEvent;
use App\Events\Examination\Exam\QuestionMethod\ExamQuestionAnnotationModifiedEvent;
use App\Events\Examination\Exam\QuestionMethod\ExamQuestionAnswerModifiedEvent;
use Laravel\Scout\Searchable;

class ExamQuestion extends Model
{
    use Searchable;
    protected $fillable = ['exam_id','score','type','partStem','stem','options','answer','sort','annotation','lock','creator_id','editor_id','ip','big','reason'];

    public function getCreator(){
        return $this -> belongsTo('App\Models\User','creator_id','id');
    }

    public function basic(){
        return $this -> hasOne('App\Home\Examination\Exam','id','exam_id');
    }

    // 获得选项
    public function getQuestionOptions(){
        return $this -> hasMany('App\Home\Examination\Exam\Question\ExamQuestionOption','qid','id')->orderBy('sort');
    }


    //新建试卷内容的创建，这里不再遵循著作和百科新建路线，而是直接新建题目
    // 触发题目1创建的事件
    protected function examQuestionCreate($exam_id,$score,$type,$partStemId,$stem,$options,$sort,$answer,$annotation,$lock,$creator_id,$editor_id,$ip,$big,$reason) {
        $result = ExamQuestion::create([
            'exam_id'   => $exam_id,
            'score'     => $score,
            'type'      => $type,
            'partStem'  => $partStemId,
            'stem'      => $stem,
            'options'   => $options,
            'sort'      => $sort,
            'answer'      => $answer,
            'annotation'  => $annotation,
            'lock'   	=> $lock,
            'creator_id' => $creator_id,
            'editor_id' => $editor_id,
            'ip'        => $ip,
            'big'       => $big,
            'reason'    => $reason,
        ]);
        event(new ExamQuestionCreatedEvent($result));
        return $result->id;
    }

    //试题题干的修改
    protected function questionStemModify($id,$stem,$lock,$editor_id,$ip,$big,$reason) {
        $result = ExamQuestion::where('id',$id)->update([
            'stem'     => $stem,
            'lock'      => $lock,
            'editor_id' => $editor_id,
            'ip'        => $ip,
            'big'       => $big,
            'reason'    => $reason,
        ]);
        event(new ExamQuestionStemModifiedEvent(ExamQuestion::find($id)));
        return $result ? '1':'0';
    }

    //试题注释的修改
    protected function questionAnnotationModify($id,$annotation,$lock,$editor_id,$ip,$big,$reason) {
        $result = ExamQuestion::where('id',$id)->update([
            'annotation'     => $annotation,
            'lock'      => $lock,
            'editor_id' => $editor_id,
            'ip'        => $ip,
            'big'       => $big,
            'reason'    => $reason,
        ]);
        event(new ExamQuestionAnnotationModifiedEvent(ExamQuestion::find($id)));
        return $result ? '1':'0';
    }

    //试题答案的修改
    protected function questionAnswerModify($id,$answer,$lock,$editor_id,$ip) {
        $result = ExamQuestion::where('id',$id)->update([
            'answer'     => $answer,
            'lock'      => $lock,
            'editor_id' => $editor_id,
            'ip'        => $ip,
        ]);
        event(new ExamQuestionAnswerModifiedEvent(ExamQuestion::find($id)));
        return $result ? '1':'0';
    }
    //试题分数的修改
    protected function questionScoreModify($id,$score,$lock,$editor_id,$ip) {
        $result = ExamQuestion::where('id',$id)->update([
            'score'     => $score,
            'lock'      => $lock,
            'editor_id' => $editor_id,
            'ip'        => $ip,
        ]);
        // event(new ExamQuestionAnswerModifiedEvent(ExamQuestion::find($id)));
        return $result ? '1':'0';
    }
}
