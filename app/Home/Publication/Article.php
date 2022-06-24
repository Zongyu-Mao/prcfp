<?php

namespace App\Home\Publication;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\ArticleCreatedEvent;
use App\Events\Publication\ArticleViewsUpdatedEvent;
use App\Events\Publication\ArticleManagerUpdatedEvent;
use Laravel\Scout\Searchable;

class Article extends Model
{
    use Searchable;
    public $timestamps = true;

    protected $fillable = ['cid','title','etitle','nature','level','content','cooperation_id','cover_id','creator_id','manage_id','lasteditor_id','stars','shares','edit_number'];

    //多对多关联用户表，取得关注词条数据
    public function articleFocus(){
        return $this->belongsToMany('App\Models\User','article_focus_users','article_id','user_id');
    }

    //关联分类表
    public function classification(){
        return $this -> hasOne('App\Home\Classification','id','cid');
    }
    //关联协作计划表
    public function cooperation(){
        return $this -> hasOne('App\Home\Publication\ArticleCooperation','aid','id');
    }

    // 一对一关联组长信息
    public function managerInfo(){
        return $this->belongsTo('App\Models\User','manage_id','id');
    }

    // 一对一得到热度信息
    public function getTemperature(){
        return $this->belongsTo('App\Home\Publication\Recommend\ArticleTemperature','id','aid');
    }

    //获取著作的封面图片
    public function articleAvatar(){
        return $this -> hasOne('App\Home\Publication\Article\articlePicture','id','cover_id');
    }
    public function avatar(){
        return $this -> hasOne('App\Home\Publication\Article\articlePicture','id','cover_id');
    }

    //多对多关联关键词表
    public function keywords(){
        return $this->belongsToMany('App\Home\Keyword','article_keywords','article_id','keyword_id');
    }

    //多对多关联用户表，取得收藏词条数据
    public function articleCollect(){
        return $this->belongsToMany('App\Models\User','article_collect_users','article_id','user_id');
    }

    //一对多关联著作内容表，取得著作的正文内容
    public function getArticleContents(){
        return $this->hasMany('App\Home\Publication\Article\ArticleContent','aid','id');
    }

    //多对多关联词条表，获得延伸阅读
    public function extendedEntryReadings(){
        return $this->belongsToMany('App\Home\Encyclopedia\Entry','article_extended_entry_readings','aid','extended_id');
    }

    //多对多关联著作表，获得延伸阅读
    public function extendedArticleReadings(){
        return $this->belongsToMany('App\Home\Publication\Article','article_extended_article_readings','aid','extended_id');
    }

    //多对多关联试卷表，获得延伸阅读
    public function extendedExamReadings(){
        return $this->belongsToMany('App\Home\Examination\Exam','article_extended_exam_readings','aid','extended_id');
    }
    //一对多关联，参考文献
    public function references(){
        return $this->hasMany('App\Home\Publication\Article\Reference\ArticleReference','content_id','id');
    }

    // 创建著作,创建著作不涉及著作实质内容
    protected function articleCreate($cid,$title,$etitle,$nature,$creator_id,$manage_id,$lasteditor_id) {
        $result = Article::create([
            'cid'   => $cid,
            'title' => $title,
            'etitle'=> $etitle,
            'nature'	=> $nature,
            'creator_id'=> $creator_id,
            'manage_id'	=> $manage_id,
            'lasteditor_id' => $lasteditor_id
        ]);
        event(new ArticleCreatedEvent($result));
        return $result->id;
    }

    //著作的编辑，此处对应仍然是正文内容第一次经过创建者编辑,coverid在create的时候已经有了
    // 补充摘要、正文第一章。这里的编辑事件将在articleContent模型中触发
    protected function articleEditor($article_id,$summary,$contentId,$lasteditor_id){
        $result = Article::where('id',$article_id)->update([
            'content'   => $contentId,
            'summary'   => $summary,
            'lasteditor_id' => $lasteditor_id
        ]);
        return $result;
    }

    //更换管理员
    protected function manageUpdate($id,$manage_id) {
        $result = Article::where('id',$id)->update([
            'manage_id'=>$manage_id
        ]);
        event(new ArticleManagerUpdatedEvent(Article::find($id)));
        return $result;
    }

    //更换封面
    protected function avatarUpdate($id,$avatar_id) {
        $result = Article::where('id',$id)->update([
            'cover_id'=>$avatar_id
        ]);
        return $result;
    }

    //xuncha
    protected function surveillance($id,$s) {
        $result = Article::where('id',$id)->update([
            'surveillance'=>$s
        ]);
        return $result;
    }
    
    protected function reviewTerminate($id) {
        $result = Article::where('id',$id)->update([
            'review_id' =>  0
        ]);
        return $result;
    }

    //更新浏览量
    protected function viewsUpdate($id,$views) {
        $result = Article::where('id',$id)->update([
            'views'   => $views
        ]);
        event(new ArticleViewsUpdatedEvent(Article::find($id)));
        return $result ? 1:0;
    }
}
