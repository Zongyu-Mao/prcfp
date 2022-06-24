<?php

namespace App\Models\Vote;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\Vote\VoteCreatedEvent;
use App\Events\Vote\VoteFinishedEvent;

class Vote extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $fillable = [
        'affiliation','type','amount','choice_limit','initiate_id','deadline', 'title', 'content','status','remark'
    ];

    // 关联选项表
    public function voteOptions(){
        return $this->hasMany('App\Models\Vote\VoteOption','vid','id');
    }
    // 关联选项表
    public function getAuthor(){
        return $this->hasOne('App\Models\User','id','initiate_id');
    }
    // 关联选项表
    public function voteRecords(){
        return $this->hasMany('App\Models\Vote\VoteRecord','vid','id');
    }

    //创建
    protected function newVote($affiliation,$type,$amount,$choice_limit,$initiate_id,$deadline,$title,$content,$remark) {
        $result = Vote::create([
            'affiliation'   => $affiliation,
            'type'   => $type,
            'amount'   => $amount,
            'choice_limit'   => $choice_limit,
            'initiate_id'   => $initiate_id,
            'deadline'   => $deadline,
            'title' => $title,
            'content' => $content,
            'remark' => $remark
        ]);
        event(new VoteCreatedEvent($result));
        return $result->id;
    }

    //结束
    protected function voteFinish($id,$status,$remark){
        $result = Vote::where('id',$id)->update([
            'status'   => $status,
            'remark'   => $remark
        ]);
        event(new VoteFinishedEvent(Vote::find($id)));
        return $result;
    }
}
