<?php

namespace App\Home\Publication\Article;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\Article\ArticleContentCreatedEvent;
use App\Events\Publication\Article\ArticleContentModifiedEvent;
use Laravel\Scout\Searchable;

class ArticleContent extends Model
{
    use Searchable;
    protected $fillable = ['aid','part_id','sort','lock','content','editor_id','ip','big','reason'];

    //多对多关联用户表，查找著作相关用户，表还没有呢
    public function users(){
        return $this -> belongsToMany('App\Models\User','content_user','content_id','user_id');
    }

    public function basic(){
        return $this -> hasOne('App\Home\Publication\Article','id','aid');
    }

    public function getCreator(){
        return $this -> belongsTo('App\Models\User','editor_id','id');
    }

    //新建著作内容的创建（摘要和正文内容），这里是正文第一章内容
    // 触发正文内容创建的事件
    protected function articleContentCreate($aid,$part_id,$sort,$lock,$content,$editor_id,$ip,$big,$reason) {
        $result = ArticleContent::create([
            'aid'       => $aid,
            'part_id'   => $part_id,
            'sort'      => $sort,
            'lock'   	=> $lock,
            'content'	=> $content,
            'editor_id' => $editor_id,
            'ip'        => $ip,
            'big'       => $big,
            'reason'    => $reason,
        ]);
        event(new ArticleContentCreatedEvent($result));
        return $result->id;
    }

    //著作内容的修改，内容的修改一般涉及的是title、content，并更改lock,更改lock放在控制器中
    protected function articleContentModify($id,$lock,$content,$editor_id,$ip,$big,$reason) {
        $result = ArticleContent::where('id',$id)->update([
            'lock'      => $lock,
            'content'   => $content,
            'editor_id' => $editor_id,
            'ip'        => $ip,
            'big'       => $big,
            'reason'    => $reason,
        ]);
        event(new ArticleContentModifiedEvent(ArticleContent::find($id)));
        return $result?1:0;
    }
}
