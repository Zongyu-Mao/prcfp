<?php

namespace App\Home\Publication;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\ArticleCooperationCreatedEvent;
use App\Events\Publication\ArticleCooperation\ArticleCooperationShutDownByManagerEvent;

class ArticleCooperation extends Model
{
    protected $fillable = ['aid','cid','title','target','timelimit','secret','deadline','ifseeking','assign','version','creator','creator_id'];

    // 关联user表，取得协作成员信息
    public function crews(){
        return $this->belongsToMany('App\Models\User','article_cooperation_users','cooperation_id','user_id');
    }
    //一对多贡献
    public function contributions(){
        return $this->hasMany('App\Models\Home\Cooperation\ArticleContributeValue','cooperation_id','id');
    }

    // 一对一关联,获得著作信息
    public function getArticle(){
        return $this->belongsTo('App\Home\Publication\Article','aid','id');
    }

    //新建著作的同时创建协作计划
    protected function articleCooperationCreate($aid,$cid,$title,$target,$secret,$timelimit,$deadline,$seeking,$assign,$version,$creator_id,$creator) {
    	$result = ArticleCooperation::create([
    		'aid' 		=> $aid,  
            'cid' 		=> $cid,
            'title'  	=> $title, 
		 	'target' 	=> $target,
		 	'secret' 	=> $secret,
		 	'timelimit' => $timelimit,
		 	'deadline' 	=> $deadline,
		 	'ifseeking' => $seeking,
            'assign'    => $assign,
		 	'version' 	=> $version,
            'creator_id'    => $creator_id,
		 	'creator' 		=> $creator
        ]);
        if(!$secret && $result->id){
            event(new ArticleCooperationCreatedEvent($result));
        }
        return $result;
    }

    //协作计划的主动关闭(管理员离职)
    protected function cooperationShutDown($id,$status) {
        $result = ArticleCooperation::where('id',$id)->update([
            'status'       => $status
        ]);
        if($result){
            event(new ArticleCooperationShutDownByManagerEvent(ArticleCooperation::find($id)));
        }
        return $result ? '1':'0';
    }
}
