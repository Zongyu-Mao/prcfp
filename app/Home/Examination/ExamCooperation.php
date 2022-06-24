<?php

namespace App\Home\Examination;

use Illuminate\Database\Eloquent\Model;
use App\Events\Examination\ExamCooperationCreatedEvent;
use App\Events\Examination\ExamCooperation\ExamCooperationShutDownByManagerEvent;

class ExamCooperation extends Model
{
    // exam 没有secret
    protected $fillable = ['exam_id','cid','title','target','timelimit','deadline','ifseeking','assign','version','creator','creator_id'];

    // 关联user表，取得协作成员信息
    public function crews(){
        return $this->belongsToMany('App\Models\User','exam_cooperation_users','cooperation_id','user_id');
    }


    //一对一关联，获得试卷信息
    public function getExam(){
        return $this->belongsTo('App\Home\Examination\Exam','exam_id','id');
    }
    //一对多贡献
    public function contributions(){
        return $this->hasMany('App\Models\Home\Cooperation\ExamContributeValue','cooperation_id','id');
    }

    //新建同时创建协作计划，协作计划内容目前不做大的变动
    protected function examCooperationCreate($exam_id,$cid,$title,$target,$timelimit,$deadline,$seeking,$assign,$version,$creator_id,$creator) {
    	$result = ExamCooperation::create([
    		'exam_id' 	=> $exam_id,  
            'cid' 		=> $cid,
            'title'  	=> $title, 
		 	'target' 	=> $target,
		 	'timelimit' => $timelimit,
		 	'deadline' 	=> $deadline,
		 	'ifseeking' => $seeking,
            'assign'    => $assign,
		 	'version' 	=> $version,
            'creator_id'    => $creator_id,
		 	'creator' 		=> $creator,
        ]);
        event(new ExamCooperationCreatedEvent($result));
        return $result;
    }

    //协作计划的主动关闭(管理员离职)
    protected function cooperationShutDown($id,$status) {
        $result = ExamCooperation::where('id',$id)->update([
            'status'       => $status
        ]);
        if($result){
            event(new ExamCooperationShutDownByManagerEvent(ExamCooperation::find($id)));
        }
        return $result ? '1':'0';
    }
}
