<?php

namespace App\Models\Vote;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\Vote\VoteRecordCreatedEvent;

class VoteRecord extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['vid','user_id','choice','createtime'];

    // 写入选项,是一个一个写入的
    protected function recordCreate($vid,$user_id,$choice,$createtime) {
        $result = VoteRecord::create([
            'vid'       => $vid,
            'user_id'     => $user_id,
            'choice'    => $choice,
            'createtime'    => $createtime
        ]);
        // 写入选项暂时不考虑触发事件
        return $result->id;
    }
}
