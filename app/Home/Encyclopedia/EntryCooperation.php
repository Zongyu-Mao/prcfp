<?php

namespace App\Home\Encyclopedia;

use Illuminate\Database\Eloquent\Model;
use App\Events\Encyclopedia\EntryCooperation\EntryCooperationCreatedEvent;
use App\Events\Encyclopedia\EntryCooperation\EntryCooperationShutDownByManagerEvent;

class EntryCooperation extends Model
{
    
    public $timestamps = true;
    //定义时间戳模型
    //protected $dateFormat = 'U';

    protected $fillable = ['eid','cid','title','target','timelimit','deadline','ifseeking','assign','creator','creator_id'];

    //一对一关联，获得词条信息
    public function getEntry(){
        return $this->belongsTo('App\Home\Encyclopedia\Entry','eid','id');
    }

    // 关联user表，取得协作成员信息
    //多对多关联关键词表
    public function crews(){
        return $this->belongsToMany('App\Models\User','entry_cooperation_users','cooperation_id','user_id');
    }
    //一对多贡献
    public function contributions(){
        return $this->hasMany('App\Models\Home\Cooperation\EntryContributeValue','cooperation_id','id');
    }

    //新建词条时新建协作计划
    protected function entryCooperationCreate($eid,$cid,$title,$target,$timelimit,$deadline,$seeking,$assign,$version,$creator_id,$creator) {
    	$result = EntryCooperation::create([
    		'eid' 		=> $eid,  
            'cid' 		=> $cid,
            'title'  	=> $title, 
		 	'target' 	=> $target,
		 	'timelimit' => $timelimit,
		 	'deadline' 	=> $deadline,
		 	'ifseeking' => $seeking,
            'assign'    => $assign,
		 	'version' 	=> $version,
            'creator_id'    => $creator_id,
		 	'creator' 		=> $creator
        ]);
        if($result->id){
            event(new EntryCooperationCreatedEvent($result));
        }
        return $result;
    }

    //协作计划的主动关闭(管理员离职)
    protected function cooperationShutDown($id,$status) {
        $result = EntryCooperation::where('id',$id)->update([
            'status'       => $status
        ]);
        if($result){
            event(new EntryCooperationShutDownByManagerEvent(EntryCooperation::find($id)));
        }
        return $result ? '1':'0';
    }
}
