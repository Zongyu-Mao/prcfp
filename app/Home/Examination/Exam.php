<?php

namespace App\Home\Examination;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\ExamCreatedEvent;
use App\Events\Examination\ExamViewsUpdatedEvent;
use App\Events\Examination\ExamManagerUpdatedEvent;
use Laravel\Scout\Searchable;

class Exam extends Model
{
    use Searchable;
    public $timestamps = true;

    protected $fillable = ['cid','title','etitle','nature','level','difficulty','total','summary','score_avg','creator_id','manage_id','lasteditor_id','stars','shares','edit_number','cover_id'];

    //多对多关联用户表，取得关注的用户数据
    public function examFocus(){
        return $this->belongsToMany('App\Models\User','exam_focus_users','exam_id','user_id');
    }

    // 一对一关联组长信息
    public function managerInfo(){
        return $this->belongsTo('App\Models\User','manage_id','id');
    }

    // 一对一得到热度信息
    public function getTemperature(){
        return $this->belongsTo('App\Home\Examination\Recommend\ExamTemperature','id','exam_id');
    }

    //获取试卷的封面图片
    public function examAvatar(){
        return $this -> hasOne('App\Home\Examination\Exam\ExamPicture','id','cover_id');
    }
    public function avatar(){
        return $this -> hasOne('App\Home\Examination\Exam\ExamPicture','id','cover_id');
    }
    //多对多关联关键词表
    public function keywords(){
        return $this->belongsToMany('App\Home\Keyword','exam_keywords','exam_id','keyword_id');
    }

    //关联分类表
    public function classification(){
        return $this -> hasOne('App\Home\Classification','id','cid');
    }

    //关联协作计划表
    public function examCooperation(){
        return $this -> hasOne('App\Home\Examination\ExamCooperation','exam_id','id');
    }

    //多对多关联用户表，取得收藏数据
    public function examCollect(){
        return $this->belongsToMany('App\Models\User','exam_collect_users','exam_id','user_id');
    }

    //一对多关联著作内容表，取得著作的正文内容
    public function getExamQuestions(){
        return $this->hasMany('App\Home\Examination\exam\examQuestion','exam_id','id');
    }

    //多对多关联试卷表，获得延伸阅读
    public function extendedExamReading(){
        return $this->belongsToMany('App\Home\Examination\Exam','exam_extended_exams','exam_id','extended_id');
    }

    //多对多关联著作表，获得延伸阅读
    public function extendedArticleReading(){
        return $this->belongsToMany('App\Home\Publication\Article','exam_extended_articles','exam_id','extended_id');
    }

    //多对多关联词条表，获得延伸阅读
    public function extendedEntryReading(){
        return $this->belongsToMany('App\Home\Encyclopedia\Entry','exam_extended_entries','exam_id','extended_id');
    }

    // 创建试卷
    protected function examCreate($cid,$title,$etitle,$summary,$nature,$creator_id,$manage_id,$lasteditor_id) {
        $result = Exam::create([
            'cid'   => $cid,
            'title' => $title,
            'etitle'=> $etitle,
            'summary'	=> $summary,
            'nature'	=> $nature,
            'creator_id'=> $creator_id,
            'manage_id'	=> $manage_id,
            'lasteditor_id' => $lasteditor_id
        ]);
        event(new ExamCreatedEvent($result));
        return $result->id;
    }

    //著作的编辑，此处对应仍然是正文内容第一次经过创建者编辑,coverid在create的时候已经有了
    // 补充摘要、正文第一章。这里的编辑事件将在examContent模型中触发
    protected function examEditor($exam_id,$summary,$contentId,$lasteditor_id){
        $result = Exam::where('id',$exam_id)->update([
            'content'   => $contentId,
            'summary'   => $summary,
            'lasteditor_id' => $lasteditor_id
        ]);
        return $result;
    }

    //更换管理员
    protected function manageUpdate($id,$manage_id) {
        $result = Exam::where('id',$id)->update([
            'manage_id'=>$manage_id
        ]);
        event(new ExamManagerUpdatedEvent(Exam::find($id)));
        return $result;
    }
    //更换封面
    protected function avatarUpdate($id,$avatar_id) {
        $result = Exam::where('id',$id)->update([
            'cover_id'=>$avatar_id
        ]);
        return $result;
    }
    //xuncha
    protected function surveillance($id,$s) {
        $result = Exam::where('id',$id)->update([
            'surveillance'=>$s
        ]);
        return $result;
    }

    // 更新分数和难度
    protected function recordUpdate($id,$score,$rate) {
        $result = Exam::where('id',$id)->update([
            'score_avg'   => $score,
            'difficulty'   => $rate,
        ]);
        // event(new ExamViewsUpdatedEvent(Exam::find($id)));
        return $result ? 1:0;
    }

    // 更新分数和难度
    protected function totalUpdate($id,$total) {
        $result = Exam::where('id',$id)->update([
            'total'   => $total,
        ]);
        // event(new ExamViewsUpdatedEvent(Exam::find($id)));
        return $result ? 1:0;
    }
    
    //更新浏览量
    protected function viewsUpdate($id,$views) {
        $result = Exam::where('id',$id)->update([
            'views'   => $views
        ]);
        event(new ExamViewsUpdatedEvent(Exam::find($id)));
        return $result ? 1:0;
    }
}
