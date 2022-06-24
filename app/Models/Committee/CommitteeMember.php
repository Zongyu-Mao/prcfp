<?php

namespace App\Models\Committee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommitteeMember extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $fillable = [
        'member_id','status'
    ];

    //创建
    protected function newMember($member_id,$status) {
        $result = CommitteeMember::create([
            'member_id'   => $member_id,
            'status'   => $status
        ]);
        // event(new VoteCreatedEvent($result));
        return $result->id;
    }
}
