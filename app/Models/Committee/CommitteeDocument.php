<?php

namespace App\Models\Committee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommitteeDocument extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $fillable = [
        'tcid','title','content','status','creator_id'
    ];

    // 关联user获得author
    public function committee(){
        return $this->belongsTo('App\Models\Committee\Committee','tcid','id');
    }

    //创建
    protected function newCommitteeDocument($tcid,$title,$content,$status,$creator_id) {
        $result = CommitteeDocument::create([
            'tcid'   => $tcid,
            'title'   => $title,
            'content'   => $content,
            'status'   => $status,
            'creator_id'   => $creator_id
        ]);
        // event(new VoteCreatedEvent($result));
        return $result->id;
    }
    // 修改
    protected function modifyCommitteeDocument($id,$tcid,$content) {
        $result = CommitteeDocument::where('id',$id)->update([
            'tcid'   => $tcid,
            'content'   => $content
        ]);
        // event(new VoteCreatedEvent($result));
        return $result;
    }
}
