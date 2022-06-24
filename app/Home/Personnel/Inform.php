<?php

namespace App\Home\Personnel;

use Illuminate\Database\Eloquent\Model;

class Inform extends Model
{
    protected $fillable = ['author_id','tcid','object_user_id','title','weight','content','url','remark','scope','belong','ground','status'];

    // 关联user表
    // 一对一关联得到作者
    public function author(){
        return $this->belongsTo('App\Models\User','author_id','id');
    }

    // 一对一关联得到对象用户
    public function getTarget(){
        return $this->belongsTo('App\Models\User','object_user_id','id');
    }

    // 一对一关联得到ground对象
    public function getEntry(){
        return $this->belongsTo('App\Home\Encyclopedia\Entry','ground','id');
    }

    // 一对一关联得到ground对象
    public function getArticle(){
        return $this->belongsTo('App\Home\Publication\Article','ground','id');
    }

    // 一对一关联得到ground对象
    public function getExam(){
        return $this->belongsTo('App\Home\Examination\Exam','ground','id');
    }

    // 一对一关联得到ground对象
    public function getGroup(){
        return $this->belongsTo('App\Home\Organization\Group\GroupDoc','ground','id');
    }

    // 多对多关联得到功章
    public function getMedals(){
        return $this->belongsToMany('App\Home\Personnel\Medal','inform_medals','inform_id','medal_id');
    }
    // 一对多得到处理记录
    public function records(){
        return $this->hasMany('App\Home\Personnel\Inform\InformOperateRecord','inform_id','id');
    }

    // 返回inform的内容链接
    protected function getInformObjectUrl($id){
    	$inform = Inform::find($id);
    	switch($inform->scope)
    	{
    		case 1:
    		$url = '/encyclopedia/reading/'.$inform->ground.'/'.$inform->getEntry->title;
    		break;
    		case 2:
    		$url = '/publication/reading/'.$inform->ground.'/'.$inform->getArticle->title;
    		break;
    		case 3:
    		$url = '/examination/reading/'.$inform->ground.'/'.$inform->getExam->title;
    		break;
    		case 4:
    		$url = '/organization/group/groupDoc/'.$inform->ground.'/'.$inform->getGroupDoc->title;
    		break;
    	}
    	return $url;
    }

    //举报信息的写入
    protected function informAdd($author_id,$tcid,$object_user_id,$title,$weight,$content,$url,$remark,$scope,$belong,$ground,$status) {
        $result = Inform::create([
            'author_id' => $author_id,
            'tcid'	=> $tcid,
            'object_user_id'	=> $object_user_id,
            'title'  	=> $title,
            'weight'  	=> $weight,
            'content'	=> $content,
            'url'		=> $url,
            'remark'    => $remark,
            'scope'		=> $scope,
            'belong'	=> $belong,
            'ground'	=> $ground,
            'status'	=> $status
        ]);
        // event
        return $result;
    }

    // 更新信息状态
    protected function updateStatus($id,$status){
        Inform::where('id',$id)->update(['status' => $status]);
    }
}
