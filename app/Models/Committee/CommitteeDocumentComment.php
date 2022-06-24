<?php

namespace App\Models\Committee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommitteeDocumentComment extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['did','title','comment','pid','author_id', 'author','createtime'];

    // 关联user获得author
    public function getAuthor(){
        return $this->belongsTo('App\Models\User','author_id','id');
    }
    // 获得document
    public function document(){
        return $this->belongsTo('App\Models\Committee\CommitteeDocument','did','id');
    }

    //评论内容的添加
    protected function commentAdd($did,$title,$comment,$pid,$author_id,$author,$createtime){
    	$result = CommitteeDocumentComment::create([
    		'did'		=> $did,
    		'title'     => $title,
    		'comment'	=> $comment,
    		'pid'		=> $pid,
            'author_id' => $author_id,
            'author' => $author,
    		'createtime'		=> $createtime,
    	]);
    	// event(new ArticleDebateCommentCreatedEvent($result));
    	return $result ? '1':'0';
    }

    //评论内容的获取
    protected function commentChild(){
        return $this-> hasMany('App\Models\Committee\CommitteeDocumentComment','pid','id');
    }

    public function allComment() {
        return $this->commentChild()->with('allComment');
    }
}
