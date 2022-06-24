<?php

namespace App\Models\Committee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommitteeMemberRecord extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'member_id','status','createtime'
    ];

    //创建
    protected function newMemberRecord($member_id,$status,$createtime) {
        $result = CommitteeMemberRecord::create([
            'member_id'   => $member_id,
            'status'   => $status,
            'createtime'   => $createtime
        ]);
        // event(new VoteCreatedEvent($result));
        return $result->id;
    }
}
