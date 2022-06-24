<?php

namespace App\Home\Encyclopedia;

use Illuminate\Database\Eloquent\Model;
use App\Events\Encyclopedia\EntryReview\EntryReviewCreatedEvent;
use App\Events\Encyclopedia\EntryReview\EntryReviewTerminatedEvent;

class EntryReview extends Model
{
    protected $fillable = ['eid','target','cid','deadline','title','content','initiate_id','initiater'];


    // 一对一关联词条,获得词条信息
    public function getEntry(){
        return $this->belongsTo('App\Home\Encyclopedia\Entry','eid','id');
    }
    // 一对多关联评审投票记录
    public function getReviewRecord(){
        return $this->hasMany('App\Home\Encyclopedia\EntryReview\EntryReviewRecord','review_id','id');
    }

    //一对一关联，获得basic信息
    public function getContent(){
        return $this->belongsTo('App\Home\Encyclopedia\Entry','eid','id');
    }

    // 一对一关联组长信息
    public function managerInfo(){
        return $this->belongsTo('App\Models\User','initiate_id','id');
    }

    //建立评审计划
    protected function reviewCreate($eid,$target,$cid,$deadline,$title,$content,$initiate_id,$initiate,$entryTitle){
        $result = EntryReview::create([
            'eid'       => $eid,
            'target'    => $target,
            'cid' => $cid,
            'deadline'  => $deadline,
            'title'     => $title,
            'content'   => $content,
            'initiate_id' => $initiate_id,
            'initiater'   => $initiate,
        ]);
        // $reviewArr = array(
        //     'eid'       => $eid,
        //     'target'    => $target,
        //     'timelimit' => $timelimit,
        //     'deadline'  => $deadline,
        //     'title'     => $title,
        //     'content'   => $content,
        //     'initiate_id' => $initiate_id,
        //     'initiater'   => $initiate, 
        // );
        // $entryReview = new EntryReview;
        // $result = $entryReview -> fill($reviewArr) -> save();
        // 这里的$result为true或false
        // 发布公告
        if($result->id){
            // 触发计划创建事件
            event(new EntryReviewCreatedEvent($result));

        }
        return $result;
    }

    // review的状态变更
    protected function reviewUpdate($id,$status){
        // 更改帮助方案为采纳
        $res = EntryReview::where('id',$id)->update([
                'status' => $status,
            ]);
        // if($res){
        //     event(new EntryReviewUpdatedEvent(EntryReview::find($id)));
        // }
        return $res ? '1':'0';
    }
    


}
