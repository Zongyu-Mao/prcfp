<?php

namespace App\Home\Publication;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\ArticleResort\ArticleResortCreatedEvent;
use App\Events\Publication\ArticleResort\ArticleResortSupportCreatedEvent;
use App\Events\Publication\ArticleResort\ArticleResortSupportAcceptedEvent;
use App\Events\Publication\ArticleResort\ArticleResortSupportRejectedEvent;

class ArticleResort extends Model
{
    protected $fillable = ['aid','cid','pid','deadline','title','content','author_id','author','status'];

    //一对一关联basic
    public function getContent(){
        return $this->belongsTo('App\Home\Publication\Article','aid','id');
    }

    // 一对一关联组长信息
    public function managerInfo(){
        return $this->belongsTo('App\Models\User','author_id','id');
    }
    // 得到帮助内容
    public function helpers() { 
        return $this->hasMany('App\Home\Publication\ArticleResort','pid','id')->with('comments');
    }
    // 得到评论内容
    public function comments() { 
        return $this->hasMany('App\Home\Publication\ArticleResort\ArticleResortSupportComment','resortId','id');
    }

    //写入新建的求助内容
    protected function resortAdd($aid,$cid,$pid,$deadline,$title,$content,$author,$author_id){
    	$resortAdd = ArticleResort::create([
            'aid'       => $aid,
            'cid'    	=> $cid,
            'pid'       => $pid,
            'title'     => $title,
            'deadline'  => $deadline,
            'content'   => $content,
            'author'    => $author,
            'author_id' => $author_id,
        ]);
        if($resortAdd->id && $resortAdd->pid == '0'){
            event(new ArticleResortCreatedEvent($resortAdd));
        }elseif($resortAdd->id && $resortAdd->pid != '0'){
            event(new ArticleResortSupportCreatedEvent($resortAdd));
        }
    	return $resortAdd ? '1':'0';
    }

    //帮助内容的接受
    protected function resortSupportAccept($id,$status){
        // 更改帮助方案为采纳
        $support = ArticleResort::where('id',$id)->update([
                'status' => '1',
            ]);
        if($support){
            event(new ArticleResortSupportAcceptedEvent(ArticleResort::find($id)));
        }
        return $support ? '1':'0';
    }

    //帮助内容的拒绝
    protected function resortSupportReject($id,$status){
        // 更改帮助方案为采纳
        $reject = ArticleResort::where('id',$id)->update([
                'status' => '2',
            ]);
        if($reject){
            event(new ArticleResortSupportRejectedEvent(ArticleResort::find($id)));
        }
        return $reject ? '1':'0';
    }
}
