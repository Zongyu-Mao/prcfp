<?php

namespace App\Home\Encyclopedia;

use Illuminate\Database\Eloquent\Model;
use App\Home\Announcement;
use App\Notifications\Encyclopedia\Entry\EntryCreated;
use App\Notifications\Encyclopedia\Entry\InterestSpecialtyEntryAdd;
use App\Notifications\Encyclopedia\Entry\EntryContentCreated;
use App\Events\Encyclopedia\EntryCreatedEvent;
use App\Events\Encyclopedia\EntryViewsUpdatedEvent;
use App\Events\Encyclopedia\EntryContentFirstCreatedEvent;
use App\Events\Encyclopedia\EntryManagerUpdatedEvent;
use App\Home\Classification;
use App\Models\User;
use Laravel\Scout\Searchable;

class Entry extends Model
{
    use Searchable;
    //定义关联的数据表
    // protected $table = 'entries';

    public $timestamps = true;

    protected $fillable = ['cid','title','etitle','creator_id','manage_id','status','edit_number','lasteditor_id'];

    //关联协作计划表
    public function entryCooperation(){
    	return $this -> hasOne('App\Home\Encyclopedia\EntryCooperation','eid','id');
    }

    // 一对一关联组长信息
    public function managerInfo(){
        return $this->belongsTo('App\Models\User','manage_id','id');
    }

    // 一对一得到热度信息
    public function getTemperature(){
        return $this->belongsTo('App\Home\Encyclopedia\Recommend\EntryTemperature','id','eid');
    }

    //关联分类表
    public function classification(){
    	return $this -> hasOne('App\Home\Classification','id','cid');
    }
    // 得到entry的picture_links,多对多
    public function links(){
        return $this->belongsToMany('App\Models\Picture\Picture','picture_entry_links','eid','picture_id');
    }

    public function entryContents(){
    	return $this -> hasMany('App\Home\Encyclopedia\Entry\EntryContent','eid','id');
    }
    // 得到巡视记录
    public function surveillances(){
        return $this -> hasMany('App\Models\Committee\Surveillance\SurveillanceRecord','sid','id');
    }
    // 得到同义词
    public function synonyms(){
        return $this -> hasMany('App\Models\Encyclopedia\Ambiguity\Synonym','eid','id');
    }
    // 得到歧义
    public function polysemants(){
        return $this -> hasMany('App\Models\Encyclopedia\Ambiguity\Polysemant','eid','id');
    }
    //关联图片表
    public function entryPictures(){
        return $this -> hasMany('App\Home\Encyclopedia\Entry\EntryPicture','eid','id');
    }

    //获取词条的封面图片
    public function entryAvatar(){
        return $this -> hasOne('App\Home\Encyclopedia\Entry\EntryPicture','id','cover_id');
    }
    public function avatar(){
        return $this -> hasOne('App\Home\Encyclopedia\Entry\EntryPicture','id','cover_id');
    }

    //多对多关联关键词表
    public function keywords(){
    	return $this->belongsToMany('App\Home\Keyword','entry_keyword','entry_id','keyword_id');
    }

    //多对多关联用户表，取得关注词条数据
    public function entryFocus(){
    	return $this->belongsToMany('App\Models\User','entry_focus_users','eid','user_id');
    }

    //多对多关联用户表，取得收藏词条数据
    public function entryCollect(){
    	return $this->belongsToMany('App\Models\User','entry_collect_users','eid','user_id');
    }

    //多对多关联自己，延伸阅读
    public function extendedEntryReadings(){
    	return $this->belongsToMany('App\Home\Encyclopedia\Entry','entry_extended_entry_readings','eid','extended_id');
    }
    //多对多关联，延伸著作阅读
    public function extendedArticleReadings(){
        return $this->belongsToMany('App\Home\Publication\Article','entry_extended_article_readings','eid','extended_id');
    }
    //多对多关联，延伸试卷阅读
    public function extendedExamReadings(){
        return $this->belongsToMany('App\Home\Examination\Exam','entry_extended_exam_readings','eid','extended_id');
    }

    //一对多关联，参考文献
    public function entryReference(){
    	return $this->hasMany('App\Home\Encyclopedia\Entry\EntryReference','entry_id','id')->orderBy('sort','asc');
    }
    public function contents(){
        return $this -> hasMany('App\Home\Encyclopedia\Entry\EntryContent','eid','id')->orderBy('sort','asc');
    }
    public function references(){
        return $this->hasMany('App\Home\Encyclopedia\Entry\EntryReference','entry_id','id')->orderBy('sort','asc');
    }
    //一对多关联，词条的动态
    public function dynamics(){
        return $this->hasMany('App\Home\Encyclopedia\EntryDynamic','eid','id');
    }


    //创建词条
    protected function entryCreate($cid,$title,$etitle,$creator_id,$manage_id,$lasteditor_id,$status) {
        $result = Entry::create([
            'cid'   => $cid,
            'title' => $title,
            'etitle'=> $etitle,
            'creator_id'=>$creator_id,
            'manage_id'=>$manage_id,
            'status'=>$status,
            'lasteditor_id' => $lasteditor_id
        ]);
        event(new EntryCreatedEvent($result));
        return $result->id;
    }
    //创建词条
    protected function entrySynonymCreate($cid,$title,$etitle,$creator_id,$status,$manage_id,$lasteditor_id) {
        $result = Entry::create([
            'cid'   => $cid,
            'title' => $title,
            'etitle'=> $etitle,
            'creator_id'=>$creator_id,
            'status'=>$status,
            'manage_id'=>$manage_id,
            'lasteditor_id' => $lasteditor_id
        ]);
        // event(new EntryCreatedEvent($result));
        return $result->id;
    }

    //词条的编辑，此处对应正文内容第一次经过创建者编辑
    protected function entryEditor($entryId,$summary,$contentId,$coverId,$lasteditor_id){
    	$result = Entry::where('id',$entryId)->update([
            'content'   => $contentId,
            'summary'   => $summary,
            'cover_id' 	=> $coverId,
            'lasteditor_id' => $lasteditor_id
        ]);
        $entry = Entry::find($entryId);
        event(new EntryContentFirstCreatedEvent($entry));
        return $result;
    }

    //更换管理员
    protected function managerUpdate($id,$manage_id) {
        $result = Entry::where('id',$id)->update([
            'manage_id'=>$manage_id
        ]);
        event(new EntryManagerUpdatedEvent(Entry::find($id)));
        return $result;
    }
    //更换封面
    protected function avatarUpdate($id,$avatar_id) {
        $result = Entry::where('id',$id)->update([
            'cover_id'=>$avatar_id
        ]);
        return $result;
    }

    //清空评审
    protected function reviewTerminate($id) {
        $result = Entry::where('id',$id)->update([
            'review_id' =>  0
        ]);
        return $result;
    }

    //xuncha
    protected function surveillance($id,$s) {
        $result = Entry::where('id',$id)->update([
            'surveillance'=>$s
        ]);
        return $result;
    }

    //更新浏览量
    protected function viewsUpdate($id,$views) {
        $result = Entry::where('id',$id)->update([
            'views'   => $views
        ]);
        // 此处浏览量计入缓存后，不触发该事件
        // event(new EntryViewsUpdatedEvent(Entry::find($id)));
        return $result ? 1:0;
    }
}
