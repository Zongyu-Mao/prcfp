<?php

namespace App\Home\Personnel;

use Illuminate\Database\Eloquent\Model;

class MessageInform extends Model
{
    protected $fillable = ['author_id','object_user_id','title','weight','content','url','remark','scope','ground_id','status'];

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
    public function getEntryReviewMessage(){
        return $this->belongsTo('App\Home\Encyclopedia\Entry\EntryReviewDiscussion','ground_id','id');
    }

    // 一对一关联得到ground对象
    public function getArticle(){
        return $this->belongsTo('App\Home\Publication\Article','ground_id','id');
    }

    // 一对一关联得到ground对象
    public function getExam(){
        return $this->belongsTo('App\Home\Examination\Exam','ground_id','id');
    }

    // 一对一关联得到ground对象
    public function getGroup(){
        return $this->belongsTo('App\Home\Organization\Group\GroupDoc','ground_id','id');
    }

    // 多对多关联得到功章
    public function getMedals(){
        return $this->belongsToMany('App\Home\Personnel\Medal','message_inform_medals','inform_id','medal_id');
    }

    // 更新信息状态
    protected function updateStatus($id,$status){
        MessageInform::where('id',$id)->update(['status' => $status]);
    }

    // 返回inform的内容链接
    protected function getInformObjectUrl($id){
    	$inform = MessageInform::find($id);
    	switch($inform->scope)
    	{
    		case 1:
    		$url = '/home/encyclopedia/entryCooperation/'.$inform->ground_id.'/'.$inform->getEntry->title.'#entryCooperationMessage'.$inform->ground_id;
    		break;
    		case 2:
    		$url = '/home/publication/articleDetail/'.$inform->ground_id.'/'.$inform->getArticle->title;
    		break;
    		case 3:
    		$url = '/home/examination/examDetail/'.$inform->ground_id.'/'.$inform->getExam->title;
    		break;
    		case 4:
    		$url = '/home/organization/group/groupDocDetail/'.$inform->ground_id.'/'.$inform->getGroupDoc->title;
    		break;
    	}
    	return $url;
    }
    // 一对多得到处理记录
    public function records(){
        return $this->hasMany('App\Home\Personnel\Inform\InformOperateRecord','inform_id','id');
    }

    //举报信息的写入
    protected function informAdd($author_id,$object_user_id,$title,$weight,$content,$url,$remark,$scope,$ground_id,$status) {
        $result = MessageInform::create([
            'author_id'	=> $author_id,
            'object_user_id'	=> $object_user_id,
            'title'  	=> $title,
            'weight'  	=> $weight,
            'content'	=> $content,
            'url'		=> $url,
            'remark'    => $remark,
            'scope'		=> $scope,
            'ground_id'	=> $ground_id,
            'status'	=> $status
        ]);
        // event(new MessageInformCreatedEvent($result));
        return $result;
    }
}
