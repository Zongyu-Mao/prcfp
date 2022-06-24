<?php

namespace App\Home\Publication;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\ArticleReview\ArticleReviewCreatedEvent;

class ArticleReview extends Model
{
    protected $fillable = ['aid','target','cid','deadline','title','content','initiate_id','initiater'];


    // 一对一关联词条,获得词条信息
    public function getArticle(){
        return $this->belongsTo('App\Home\Publication\Article','aid','id');
    }
    // 一对多关联评审投票记录
    public function getReviewRecord(){
        return $this->hasMany('App\Home\Publication\ArticleReview\ArticleReviewRecord','review_id','id');
    }

    //一对一关联，获得basic信息
    public function getContent(){
        return $this->belongsTo('App\Home\Publication\Article','aid','id');
    }

    // 一对一关联组长信息
    public function managerInfo(){
        return $this->belongsTo('App\Models\User','initiate_id','id');
    }

    //建立评审计划
    protected function reviewCreate($aid,$target,$cid,$deadline,$title,$content,$initiate_id,$initiate,$entryTitle){
        $result = ArticleReview::create([
            'aid'       => $aid,
            'target'    => $target,
            'cid' => $cid,
            'deadline'  => $deadline,
            'title'     => $title,
            'content'   => $content,
            'initiate_id' => $initiate_id,
            'initiater'   => $initiate,
        ]);
        event(new ArticleReviewCreatedEvent($result));
        return $result->id;
    }
}
